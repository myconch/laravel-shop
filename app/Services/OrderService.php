<?php

namespace App\Services;

use App\Exceptions\InternalException;
use App\Jobs\RefundInstallmentOrder;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Order;
use App\Models\ProductSku;
use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use Carbon\Carbon;
use App\Models\CouponCode;
use App\Exceptions\CouponCodeUnavailableException;

class OrderService
{
    public function store(User $user,UserAddress $address,$remark,$items,CouponCode $coupon = null)
    {
        $user = $user->load(['userAndMasters']);
        //dd($user->userAndMasters);
        //如果传入了优惠券，则先检测是否可用
        if ($coupon) {
            //但此时我们还没计算出订单总额，因此先不校验
            $coupon->checkAvailable();
        }
        //开启一个数据库事务
        $order = \DB::transaction(function () use ($user,$address,$remark,$items,$coupon) {
            //更新此地址的最后使用时间
            $address->update(['last_used_at'=> Carbon::now()]);
            //创建一个订单
            $order = new Order([
                'address' => [  //将此地址信息放入订单中
                    'address' => $address->full_address,
                    'zip' => $address->zip,
                    'contact_name' => $address->contact_name,
                    'contact_phone' => $address->contact_phone,
                ],
                'remark' => $remark,
                'total_amount' => 0,
            ]);
            //订单关联到当前用户
            $order->user()->associate($user);
            $order->save();

            $totalAmount = 0;
            // 店主折扣，没有则为1
            $percent = 1;
            // 若$user->userAndMasters计数为空则表示不存在
            if(count($user->userAndMasters)) {
                $percent = 1 + $user->userAndMasters[0]->percent;
            }

            //遍历用户提交的sku
            foreach($items as $data) {
                $sku = ProductSku::find($data['sku_id']);
                //创建一个OrderItem并直接与当前订单关联
                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price' => $sku->price*$percent,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                // 计算总价时，算上店主折扣
                $totalAmount += $sku->price * $data['amount'] *$percent;
                if ($sku->decreaseStock($data['amount']) <= 0 ){
                    throw new InvalidRequestException('该商品库存不足');
                }
            }
            if ($coupon) {
                //总金额已经计算出来了，检查是否符合优惠券规则
                $coupon->checkAvailable($user,$totalAmount);
                //把订单金额修改为优惠后的金额
                $totalAmount = $coupon->getAdjustedPrice($totalAmount);
                //将订单与优惠券关联
                $order->couponCode()->associate($coupon);
                //增加优惠券的用量，需要判断返回值
                if ($coupon->changeUsed() <= 0) {
                    throw new CouponCodeUnavailableException('该优惠券已兑完');
                }
            }
            //更新订单总额
            $order->update(['total_amount' => $totalAmount]);

            //将下单商品从购物车中移除
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);

            return $order;
        });

        //这里直接使用dispatch函数
        dispatch(new CloseOrder($order,config('app.order_ttl')));

        return $order;
    }

    public function refundOrder(Order $order)
    {
        // 判断该订单的支付方式
        switch ($order->payment_method) {
            case 'wechat':
                // 微信的先留空
                // todo
                break;
            case 'alipay':
                // 用我们刚刚写的方法来生成一个退款订单号
                $refundNo = Order::getAvailableRefundNo();
                // 调用支付宝支付实例的 refund 方法

                $ret = app('alipay')->refund([
                    'out_trade_no' => $order->no, // 之前的订单流水号
                    'refund_amount' => $order->total_amount, // 退款金额，单位元
                    'out_request_no' => $refundNo, // 退款订单号
                ]);
                // 根据支付宝的文档，如果返回值里有 sub_code 字段说明退款失败
                if ($ret->sub_code) {
                    // 将退款失败的保存存入 extra 字段
                    $extra = $order->extra;
                    $extra['refund_failed_code'] = $ret->sub_code;
                    // 将订单的退款状态标记为退款失败
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_FAILED,
                        'extra' => $extra,
                    ]);
                } else {
                    // 将订单的退款状态标记为退款成功并保存退款订单号
                    $order->update([
                        'refund_no' => $refundNo,
                        'refund_status' => Order::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;
            case 'installment':
                $order->update([
                    'refund_no' => Order::getAvailableRefundNo(),
                    'refund_status' => Order::REFUND_STATUS_PROCESSING,
                ]);
                //触发退款异步任务
                dispatch(new RefundInstallmentOrder($order));
                break;
            default:
                // 原则上不可能出现，这个只是为了代码健壮性
                throw new InternalException('未知订单支付方式：' . $order->payment_method);
                break;
        }
    }
}
