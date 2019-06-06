<?php

namespace Tests\Feature;

use App\User;
use Tests\Support\Prepare;

class LogoutTest extends Prepare
{

    public function testNotAuthenticatedUserCanNotLogout(): void
    {
        $response = $this->post('/logout');
        $response->assertRedirect('/');
    }

    public function testAuthenticatedUserCanLogout(): void
    {
        $response = $this->actingAs(User::findOrFail(1))->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

}
