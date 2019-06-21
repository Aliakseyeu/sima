<?php

use Faker\Generator as Faker;

$factory->define(App\Item::class, function (Faker $faker) {
    return [
        'order_id' => App\Order::inRandomOrder()->first(),
        'pid' => $faker->randomNumber,
        'sid' => $faker->randomNumber,
        'info' => json_encode((object)['price' => 100])
    ];
});
