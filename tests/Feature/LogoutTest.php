<?php

namespace Tests\Feature;

use Tests\TestCase;

class LogoutTest extends TestCase
{

    protected $userRepository;
    protected $user;

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->userRepository->getUser()->save();
    }

    public function testNotAuthenticatedUserCanNotLogout(): void
    {
        $response = $this->post('/logout');
        $response->assertRedirect('/');
    }

    public function testAuthenticatedUserCanLogout(): void
    {
        $response = $this->actingAs($this->userRepository->getUser())->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

}
