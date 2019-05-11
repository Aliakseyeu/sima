<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Repositories\UserRepository;
use App\User;
use Faker;

class LoginTest extends TestCase
{

    //todo repository

    use Traits\UserTrait;

    public function setUp()
    {
        parent::setUp();
        dd(bcrypt('ok'));
    }

    public function testIsLoginPageAvailable(): void
    {
        dd(bcrypt('ok'));
        $response = $this->get('/login');
        $response->assertOk();
    }




    /*public function testUserCanNotLoginWithEmptyData(): void
    {
        $response = $this->from('/login')->post('/login', []);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function testUserCanNotLoginWithWrongEmail(): void
    {
        $response = $this->from('/login')
            ->post('/login', [
                'email' => $this->user->email.'asd', 
                'password' => $this->password
            ]);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function testUserCanNotLoginWithWrongPassword(): void
    {
        $response = $this->from('/login')
            ->post('/login', [
                'email' => $this->user->email, 
                'password' => 'wrong-pass'
            ]);
        $response->assertRedirect('/login');
        $this->assertGuest();        
    }

    public function testUserCanLoginWithCorrectData(): void
    {
        $response = $this->post('/login', [
            'email' => $this->user->email,
            'password' => $this->password,
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($this->user);
    }

    public function testAuthenticatedUserCanNotSeeLoginPage():void
    {
        $response = $this->actingAs($this->user)->get('/login');
        $response->assertRedirect('/');
    }*/

    
}
