<?php

namespace Tests\Feature;

use Tests\TestCase;

class IndexTest extends TestCase
{

    use Traits\DbPreparingTrait;

    public function testIsIndexPageAvailable()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
