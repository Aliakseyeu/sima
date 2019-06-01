<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 01.06.19
 * Time: 11:38
 */

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class PrepareDatabase extends TestCase
{

    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->artisan('migrate:refresh', ['--env'=>'testing']);
        $this->artisan('db:seed', ['--env'=>'testing']);
    }

}