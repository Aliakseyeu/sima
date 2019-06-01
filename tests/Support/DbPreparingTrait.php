<?php

namespace Tests\Support;

use Illuminate\Foundation\Testing\RefreshDatabase;

trait DbPreparingTrait
{

    public function __construct()
    {
        $this->artisan('migrate:refresh', ['--env'=>'testing']);
        $this->artisan('db:seed', ['--env'=>'testing']);
    }
}