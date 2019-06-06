<?php

use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{

    public function run()
    {
        factory(App\Order::class, 5)->create()->each(function($order, $key){
            $order->item()->save(factory(App\Item::class)->create(['order_id' => $order->id]));
            for($i = 0; $i <= $key; $i++){
                $order->users()->attach(
                    App\User::findOrFail($i+3),
                    [
                        'qty'=>rand(1, 10),
                        'delivery'=>rand(10, 50),
                        'delivery_info'=>json_encode((object)[])
                    ]
                );
            }
        });
    }

}
