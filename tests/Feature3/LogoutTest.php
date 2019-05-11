<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Repositories\UserRepository;

class LogoutTest extends TestCase
{

    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function testNotAuthenticatedUserCanNotLogout(): void
    {
        $response = $this->post('/logout');
        $response->assertRedirect('/');
    }

    public function testAuthenticatedUserCanLogout(): void
    {
        $response = $this->userRepository->authenticateByTest()->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

}
