<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use App\User;
use Faker;

class LoginTest extends TestCase
{

    protected $userRepository;

    public function setUp()
    {
        parent::setUp();
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
        $response = $this->from('/login')
            ->post('/login', [
                'email' => $this->userRepository->getUser()->email.'asd', 
                'password' => $this->userRepository->getPassword()
            ]);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function testUserCanNotLoginWithWrongPassword(): void
    {
        $response = $this->from('/login')
            ->post('/login', [
                'email' => $this->userRepository->getUser()->email, 
                'password' => 'wrong-pass'
            ]);
        $response->assertRedirect('/login');
        $this->assertGuest();        
    }

    public function testUserCanLoginWithCorrectData(): void
    {
        $response = $this->post('/login', [
            'email' => $this->userRepository->getUser()->email,
            'password' => $this->userRepository->getpassword(),
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($this->userRepository->getUser());
    }

    public function testAuthenticatedUserCanNotSeeLoginPage():void
    {
        $response = $this->actingAs($this->userRepository->getUser())->get('/login');
        $response->assertRedirect('/');
    }

    
}
