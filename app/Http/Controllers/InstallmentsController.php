<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Installment;
use App\Exceptions\InvalidRequestException;
use App\Events\OrderPaid;

class InstallmentsController extends Controller
{
    public function index(Request $request)
    {
        $installments = Installment::query()
                ->where('user_id',$request->user()->id)
                ->orderBy('created_at','desc')
                ->paginate(10);

        return view('installments.index',['installments'=>$installments]);
    }

    public function show(Installment $installment)
    {
        $this->authorize('own',$installment);
        $items = $installment->items()
                                    ->orderBy('sequence')
                                    ->get();
        //dd($items);
        return view('installments.show',[
            'installment'=>$installment,
            'items'=>$items,
            //下一个未完成还款的还款计划
            'nextItem' => $items->where('paid_at',null)->first(),
            ]);
    }

    public function payByAlipay(Installment $installment)
    {
        //获取当前分期最近的一个未支付的还款计划
        $nextItem = $installment->items()->where('paid_at',null)
                                        ->orderBy('sequence')
                                        ->first();

        if ($installment->order->closed){
            throw new InvalidRequestException('订单已关闭');
        }
        if ($installment->status === Installment::STATUS_FINISHED) {
            throw new InvalidRequestException('该分期订单已结清');
        }
        if (!$nextItem) {
            //如果没有未支付的还款，原则上是不可能的，因为如果已经结清，上一个判断就结束了
            throw new InvalidRequestException('该分期订单已结清');
        }

        return app('alipay')->web([
            'out_trade_no' => $installment->no.'_'.$nextItem->sequence,
            'total_amount' => $nextItem->total,
            'subject' => 'laravel-shop 分期支付订单：'.$installment->no,
            //这里的 notify_url 和 return_url 可以覆盖掉 AppServiceProvider 设置的回调地址
            'notify_url' => ngrok_url('installments.alipay.notify'),
            'return_url' => route('installments.alipay.return'),
        ]);
    }

    //支付宝前端回调
    public function alipayReturn()
    {
        try {
            app('alipay')->verify();
        } catch (\Exception $e) {
            return view('pages.error',['msg' => '数据不正确']);
        }

        return view('pages.success',['msg' => '付款成功']);
    }

    //服务器端回调
    public function alipayNotify()
    {
        //校验输入参数
        $data = app('alipay')->verify();
        //如果订单状态不是成功或结束，则不走后续逻辑
        //所以交易状态：https：//docs.open.alipay.com/59/103672
        if (!in_array($data->trade_status,['TRADE_SUCCESS','TRADE_FINISHED'])) {
            return app('alipay')->success();
        }
        // 拉起支付时使用的支付订单号是由分期流水号 + 还款计划编号组成的
        // 因此可以通过支付订单号来还原出这笔还款是哪个分期付款的哪个还款计划
        list($no,$sequence) = explode('_',$data->out_trade_no);
        $installment = Installment::where('no',$no)->first();
        if (!$installment){
            //正常来说不可能出现支付一笔不存在的订单，这个判断只是加强系统的健壮性
            return 'fail';
        }
        // 根据还款计划编号查询对应的还款计划，原则上不会找不到，这里的判断只是增强代码健壮性
        if (!$item = $installment->items()->where('sequence',$sequence)->first()) {
            return 'fail';
        }
        // 如果这个还款计划的支付状态是已支付，则告知支付宝此订单已完成，并不再执行后续逻辑
        if ($item->paid_at) {
            return app('alipay')->success();
        }
        //使用事务，保证数据一致性
        \DB::transaction(function () use ($data,$no,$installment,$item) {
            //更新对应的还款计划
            $item->update([
                'paid_at' => Carbon::now(),
                'payment_method' => 'alipay',
                'payment_no' => $data->trade_no,   //支付宝订单号
            ]);
            //如果这是第一笔还款
            if ($item->sequence === 1) {
                $installment->update([
                    'status' => Installment::STATUS_REPAYING,
                ]);
                $installment->order()->update([
                    'paid_at' => Carbon::now(), //支付时间
                    'payment_method' => 'installment',  //支付方式
                    'payment_no' => $no, //支付宝订单号
                ]);
                //触发商品订单已支付事件
                event(new OrderPaid($installment->order));
            }
            //如果这是最后一笔还款
            if ($item->sequence === $installment->count) {
                $installment->update([
                    'status' => Installment::STATUS_FINISHED,
                ]);
            }
        });

        return app('alipay')->success();
    }
}
