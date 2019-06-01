<?php

use Faker\Generator as Faker;

$factory->define(App\Order::class, function (Faker $faker) {
    return [
        'group_id' => App\Group::first()->id
    ];
});
