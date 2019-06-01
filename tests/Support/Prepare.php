<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 01.06.19
 * Time: 12:54
 */

namespace Tests\Support;

use Tests\TestCase;

class Prepare extends TestCase
{

    public function setUp()
    {
        parent::setUp();

        $uses = array_flip(class_uses_recursive(static::class));

        if (isset($uses[UserTrait::class])) {
            $this->userTrait();
        }
    }

}