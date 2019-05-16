<?php

namespace Tests\Feature;

class LogoutTest extends BaseUser
{

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
