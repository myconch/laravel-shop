<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\OrderPaidNotification;

//implements ShouldQueue 代表异步监听
class SendOrderPaidMail implements ShouldQueue
{
    public function handle(OrderPaid $event)
    {
        //从事件对象中取出对应的订单
        $order=$event->getOrder();
        //调用notify方法来发送通知
        $order->user->notify(new OrderPaidNotification($order));
    }
}
