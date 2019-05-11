<?php

namespace Tests\Feature;

use App\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ArchiveTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testIsArchivePageAvailable()
    {
        $response = $this->get('/archive');
        $response->assertRedirect('/login');

        $user = User::find(1);
        $response = $this->actingAs($user)
            ->get('/archive');
        $response->assertOk();
    }
}
