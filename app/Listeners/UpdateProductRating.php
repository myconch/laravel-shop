<?php

namespace App\Listeners;

use App\Events\OrderReviewed;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use DB;
use App\Models\OrderItem;


//implements ShouldQueue 代表这个处理器是异步的
class UpdateProductRating implements ShouldQueue
{
    public function handle(OrderReviewed $event)
    {
        //通过with方法提前加载数据，避免N+1的问题
        $items = $event->getOrder()->items()->with(['product'])->get();
        foreach($items as $item) {
            $result = OrderItem::query()
                ->where('product_id',$item->product_id)
                ->whereHas('order',function ($query) {
                    $query->whereNotNull('paid_at');
                })
                ->first([
                    DB::raw('count(*) as review_count'),
                    DB::raw('avg(rating) as rating')
                ]);
            //更新商品的评分和评价数
            $item->product->update([
                'rating' => $result->rating,
                'review_count' => $result->review_count,
            ]);
        }
    }
}
