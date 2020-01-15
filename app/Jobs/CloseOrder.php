<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Order;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $delay)
    {
        $this->order = $order;
        // 设置延迟的时间，delpy() 方法的参数代表多少秒之后执行
        $this->delay($delay);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // 判断对应的订单是否已被支付
        // 如果已经支付则不需要关闭订单，直接退出
        if ($this->order->paid_at) {
            return;
        }

        // 通过事务执行 sql
        \DB::transaction(function(){
            // 将订单的 closed 字段标记为 true,即关闭订单
            $this->order->update(['closed' => true]);
            // 循环遍历订单中的商品 SKU，将订单中的数量加回到 SKU的库存中去
            foreach ($this->order->items as $item) {
                $this->productSku->addStock($item->amount);
            }
        });

    }
}
