<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Installment;
use App\Models\InstallmentItem;
use App\Models\Order;
use App\Exceptions\InternalException;

class RefundInstallmentOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //若订单的状态不是是 未付款 或 未申请退款 或 installment支付 ，则不执行该函数
        //dfafa

        if (!$this->order->paid_at || $this->order->refund_status !== Order::REFUND_STATUS_PROCESSING || $this->order->payment_method !== 'installment') {
            return;
        }

        /*
        if ($this->order->payment_method !== 'installment'
            || !$this->order->paid_at
            || $this->order->refund_status !== Order::REFUND_STATUS_PROCESSING) {
            return;
        }
        */

        //原则上不会找不到对应的分期付款，此处只是为了代码的健壮性
        if (!$installment = Installment::query()->where('order_id',$this->order->id)->first()) {
            return;
        }
        foreach ($installment->items as $item) {
            if (!$item->paid_at || in_array($item->refund_status,[InstallmentItem::REFUND_STATUS_SUCCESS,InstallmentItem::REFUND_STATUS_PROCESSING])) {
                continue;
            }
            //调用具体的退款逻辑
            try{
                $this->refundInstallmentItem($item);
            } catch (\Exception $e) {
                \Log::warning('分期退款失败：'.$e->getMessage(),['installment_item_id'=>$item->id,]);
                //假如某个还款计划退款报错了，则暂时跳过，继续处理下一个还款计划
                continue;
            }
        }
        $installment->refreshRefundStatus();
    }

    protected function refundInstallmentItem(InstallmentItem $item)
    {
        //退款单号使用商品订单退款单号与当前还款计划需要拼接而成
        $refundNo = $this->order->refund_no.'_'.$item->sequence;
        switch ($item->payment_method) {
            case 'wechat':
                //todo
                break;
            case 'alipay':
                $ret = app('alipay')->refund([
                    'trade_no' => $item->payment_no, //使用支付宝交易号来退款
                    'refund_amount' => $item->base,
                    'out_request_no' => $refundNo, //退款订单号
                ]);
                //返回值里有sub_code字段说明退款失败
                if ($ret->sub_code) {
                    $item->update([
                        'refund_status' => InstallmentItem::REFUND_STATUS_FAILED,
                    ]);
                } else {
                    $item->update([
                        'refund_status' => InstallmentItem::REFUND_STATUS_SUCCESS,
                    ]);
                }
                break;
            default:
                //原则上不可能出现，这个只是为了代码的健壮性
                throw new InternalException('未知订单支付方式：'.$item->payment_method);
                break;
        }
    }
}
