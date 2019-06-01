<?php
/**
 * Created by PhpStorm.
 * User: Olga
 * Date: 31.05.2019
 * Time: 21:13
 */

namespace Tests\Feature\Traits;

use Illuminate\Foundation\Testing\RefreshDatabase;

trait DbPreparingTrait
{
    use RefreshDatabase{
        RefreshDatabase::refreshDatabase as protected __rdConstruct;
    }

    public function dbPreparingTrait()
    {
        $this->__rdConstruct();
        $this->artisan('migrate', ['--env'=>'testing']);
        $this->artisan('db:seed', ['--env'=>'testing']);
    }
}