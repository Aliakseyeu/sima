<?php

use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{

    public function run()
    {
        factory(App\Order::class, 5)->create()->each(function($item, $key){
            for($i = 0; $i <= $key; $i++){
                $item->users()->attach(
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
