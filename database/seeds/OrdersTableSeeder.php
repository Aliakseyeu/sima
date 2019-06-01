<?php

use Illuminate\Database\Seeder;

class OrdersTableSeeder extends Seeder
{

    public function run()
    {
        factory(App\Order::class, 5)->create()->each(function($item){
            $count = rand(1, 5);
            for($i = 1; $i <= $count; $i++){
                $item->users()->attach(
                    App\User::inRandomOrder()->first(),
                    [
                        'qty'=>7,
                        'delivery'=>10,
                        'delivery_info'=>json_encode((object)[])
                    ]
                );
            }
        });
    }

}
