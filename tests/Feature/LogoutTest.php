<?php

namespace Tests\Feature;

use Tests\Support\Prepare;
use Tests\Support\UserTrait;

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
