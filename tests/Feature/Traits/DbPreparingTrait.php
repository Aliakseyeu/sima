<?php
/**
 * Created by PhpStorm.
 * User: Olga
 * Date: 31.05.2019
 * Time: 21:13
 */

namespace Tests\Feature\Traits;


trait DbPreparingTrait
{
    public function dbPreparingTrait()
    {
        $this->artisan('migrate', ['--env'=>'testing']);
        $this->artisan('db:seed', ['--env'=>'testing']);
    }
}