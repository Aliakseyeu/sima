<?php

namespace Tests\Feature;


class IndexTest extends BaseUser
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
