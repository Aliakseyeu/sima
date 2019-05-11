<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Repositories\UserRepository;

class LogoutTest extends TestCase
{

    protected $userRepository;
    protected $user;

    /*public function setUp()
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->user = $this->userRepository->getTestUser();
    }

    public function testNotAuthenticatedUserCanNotLogout(): void
    {
        $response = $this->post('/logout');
        $response->assertRedirect('/');
    }

    public function testAuthenticatedUserCanLogout(): void
    {
        $response = $this->actingAs($this->user)->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }*/

}
