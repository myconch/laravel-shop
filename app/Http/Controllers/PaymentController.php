<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Exceptions\InvalidRequestException;
use Carbon\Carbon;
use App\Models\Installment;
use Illuminate\Validation\Rule;

class PaymentController extends Controller
{
    public function payByAlipay(Order $order,Request $request)
    {
        //判断订单是否属于当前用户
        $this->authorize('own',$order);
        //订单已支付或已关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }

        //调用支付宝的网页支付
        return app('alipay')->web([
            'out_trade_no' =>  $order->no,  //订单编号，需要保证在商户端不重复
            'total_amount' => $order->total_amount,  //订单金额
            'subject' => '支付 Laravel Shop 的订单：'.$order->no,  //订单标题
        ]);
    }

    //前端回调页面
    public function alipayReturn()
    {
        try{
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error',['msg'=>'数据不正确']);
        }

        return view('pages.success',['msg'=>'付款成功']);
    }

    //服务器端回调
    public function alipayNotify()
    {
        //校验输入参数
        $data = app('alipay')->verify();
        \Log::debug('测试', $data->all());
        //如果订单状态不是成功或结束，则不走后续逻辑
        //所以交易状态：https：//docs.open.alipay.com/59/103672
        if (!in_array($data->trade_status,['TRADE_SUCCESS','TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        //$data->out_trade_no 拿到订单流水号，并在数据库中查询
        $order = Order::where('no',$data->out_trade_no)->first();
        //正常来说不可能出现支付一笔不存在的订单，这个判断只是加强系统的健壮性
        if (!$order) {
            return 'fail';
        }
        //如果这笔订单的状态已经是已支付
        if ($order->paid_at) {
            //返回数据给支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at' => Carbon::now(), //支付时间
            'payment_method' => 'alipay',  //支付方式
            'payment_no' => $data->trade_no, //支付宝订单号
        ]);

        $this->afterPaid($order);

        return app('alipay')->success();
    }

    //
    public function wechatNotify()
    {
        //
    }

    protected function afterPaid(Order $order)
    {
        event(new OrderPaid($order));
    }

    public function payByInstallment(Order $order,Request $request)
    {
        //判断订单是否属于当前用户
        $this->authorize('own',$order);
        //判断订单已支付或已关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }
        //判断订单金额是否满足分期的最低要求
        if ($order->total_amount < config('app.min_installment_amount')) {
            throw new InvalidRequestException('订单金额不满足分期要求');
        }
        //校验用户还款月数，数值必须是我们配置好费率的期数
        $this->validate($request,[
            'count' => ['required',Rule::in(array_keys(config('app.installment_fee_rate')))],
        ]);
        //删除同一笔订单发起过其他的状态是未支付的分期付款，避免一笔订单有多个分期付款
        Installment::query()
            ->where('order_id',$order->id)
            ->where('status',Installment::STATUS_PENDING)
            ->delete();

        $count = $request->input('count');
        //创建一个新的分期付款对象
        $installment = new Installment([
            'total_amount' => $order->total_amount,
            'count' => $count,
            'fee_rate' => config('app.installment_fee_rate')[$count],
            'fine_rate' => config('app.installment_fine_rate'),
        ]);
        $installment->user()->associate($request->user());
        $installment->order()->associate($order);
        $installment->save();

        $due_date = Carbon::tomorrow();
        $base = big_number($order->total_amount)->divide($count)->getValue();
        $fee = big_number($base)->multiply($installment->fee_rate)->divide(100)->getValue();

        for($i=0; $i < $count;$i++){
            //最后一期要用总本金减去前面几期的本金，因为总金额不一定刚好能被期数整除
            if ($i ===$count-1){
                $base = big_number($order->total_amount)->subtract(big_number($base)->multiply($count-1))->getValue();
            }
            $installment->items()->create([
                'sequence' => $i+1,
                'base' => $base,
                'fee' => $fee,
                'due_date' => $due_date,
            ]);

            $due_date = $due_date->copy()->addDays(30);
        }

        return $installment;
    }

}
