<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Repositories\UserRepository;

class AuthTest extends TestCase
{

    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function testIsLoginPageAvailable(): void
    {
        $response = $this->get('/login');
        $response->assertOk();
    }

    public function testUserCanNotLoginWithEmptyData(): void
    {
        $response = $this->from('/login')->post('/login', []);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function testUserCanNotLoginWithWrongEmail(): void
    {
        $response = $this->from('/login')->post('/login', ['email' => env('USER_WRONG_EMAIL'), 'password' => env('USER_PASS')]);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function testUserCanNotLoginWithWrongPassword(): void
    {
        $response = $this->from('/login')->post('/login', ['email' => env('USER_EMAIL'), 'password' => env('USER_WRONG_PASS')]);
        $response->assertRedirect('/login');
        $this->assertGuest();        
    }

    public function testUserCanLoginWithCorrectData(): void
    {
        $response = $this->post('/login', [
            'email' => env('USER_EMAIL'),
            'password' => env('USER_PASS'),
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($this->userRepository->getTestUser()->first());
    }

    public function testAuthenticatedUserCanNotSeeLoginPage():void
    {
        $response = $this->userRepository->authenticateByNew()->get('/login');
        $response->assertRedirect('/');
    }

    
}
