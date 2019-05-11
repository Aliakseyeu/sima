<?php

namespace Tests\Feature\Repositories;

use DB;
use App\{Order};
/*use App\Repositories\ItemRepository;
use Illuminate\Database\Eloquent\Collection;*/

class OrderRepository
{

    protected function removeTestUserOrders(): void
    {
        $user = $this->getTestUser()->first();
        $ordersIds = DB::table('order_user')->where('user_id', $user->id)->get()->map(function($item){
            return $item->order_id;
        });
        $orders = Order::whereIn('id', $ordersIds)->get();
        foreach($orders as $order){
            if($order->users->count() == 1){
                DB::transaction(function() use ($order){
                    $order->users->first()->pivot->delete();
                    $order->item->delete();
                    $order->delete();
                });
            } else {
                $order->users->where('id', $user->id)->first()->pivot->delete();
            }
        }
    }
    
}
