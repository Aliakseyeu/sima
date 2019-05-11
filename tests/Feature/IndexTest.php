<?php

namespace Tests\Feature;

use Tests\TestCase;

class IndexTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIsIndexPageAvailable()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
}
