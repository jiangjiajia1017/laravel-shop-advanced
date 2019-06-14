<?php

namespace App\Listeners;

use App\Events\OrderPaid;
use App\Models\Order;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;

class UpdateCrowdfundingProductProgress implements ShouldQueue
{

    /**
     * Handle the event.
     *
     * @param  OrderPaid  $event
     * @return void
     */
    public function handle(OrderPaid $event)
    {
        $order = $event->getOrder();

        //非众筹订单无需更新
        if($order->type !== Order::TYPE_CROWDFUNDING){
            return ;
        }

        $crowdfunding = $order->items[0]->product->crowdfunding;

        $data = Order::query()
            ->where('type', Order::TYPE_CROWDFUNDING)
            ->whereNotNull('paid_at')
            ->whereHas('items',function ($query) use ($crowdfunding){
                $query->where('product_id', $crowdfunding->product_id);
            })
            ->first(
                [
                    DB::raw('sum(total_amount) as total_amount'),
                    DB::raw('count(distinct(user_id)) as User_count')
                ]
            );
        $crowdfunding->update([
            'total_amount' => $data->total_amount,
            'user_count'   => $data->user_count,
        ]);

    }
}
