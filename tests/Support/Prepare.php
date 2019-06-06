<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 01.06.19
 * Time: 12:54
 */

namespace Tests\Support;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class Prepare extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->runArtisan();
    }

    protected function runArtisan(): void
    {
        $this->artisan('migrate:refresh', ['--env'=>'testing']);
        $this->artisan('db:seed', ['--env'=>'testing']);
    }

    protected function runTraits(): void
    {
        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[UserTrait::class])) {
            $this->userTrait();
        }

        if (isset($uses[GroupTrait::class])) {
            $this->groupTrait();
        }

        if (isset($uses[ItemTrait::class])) {
            $this->itemTrait();
        }

        if (isset($uses[OrderTrait::class])) {
            $this->orderTrait();
        }
    }

}