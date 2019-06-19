<?php

namespace Tests\Feature;

use App\User;
use Tests\Support\{Prepare, UserTrait};

class LogoutTest extends Prepare
{

    use UserTrait;

    public function testNotAuthenticatedUserCanNotLogout(): void
    {
        $response = $this->post('/logout');
        $response->assertRedirect('/');
    }

    public function testAuthenticatedUserCanLogout(): void
    {
        $response = $this->actingAs($this->getUser())->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

}
