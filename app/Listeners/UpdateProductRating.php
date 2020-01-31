<?php

namespace App\Listeners;

use App\Events\OrderReviewed;
use App\Models\OrderItem;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;

class UpdateProductRating implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  OrderReviewed  $event
     * @return void
     */
    public function handle(OrderReviewed $event)
    {
        // 提前获取数据 避免 N + 1 问题
        $items = $event->getOrder()->items()->with(['product'])->get();

        foreach ($items as $item){
            $result = OrderItem::query()
                ->where('product_id',$item->product_id)
                ->whereNotNull('reviewed_at')
                ->whereHas('order', function ($query){
                    $query->whereNotNull('paid_at');
                })
                ->first([
                    DB::raw('count(*) as review_count'),
                    DB::raw('avg(rating) as rating')
                ]);
            // 更新商品的评分和评价数
            $item->product->update([
                'rating' => $result->rating,
                'review_count' => $result->review_count
            ]);
        }

    }
}
