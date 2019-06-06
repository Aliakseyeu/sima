<?php

namespace Tests\Feature;

use App\User;
use Tests\Support\Prepare;
use Tests\Support\UserTrait;

class LoginTest extends Prepare
{

    // use UserTrait;

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
                'email' => User::findOrFail(1)->email.'asd', 
                'password' => env('PASSWORD')
            ]);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function testUserCanNotLoginWithWrongPassword(): void
    {
        $response = $this->from('/login')
            ->post('/login', [
                'email' => User::findOrFail(1)->email, 
                'password' => 'wrong-pass'
            ]);
        $response->assertRedirect('/login');
        $this->assertGuest();        
    }

    public function testUserCanLoginWithCorrectData(): void
    {
        $response = $this->post('/login', [
            'email' => ($user = User::findOrFail(1))->email,
            'password' => env('PASSWORD'),
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function testAuthenticatedUserCanNotSeeLoginPage():void
    {
        $response = $this->actingAs(User::findOrFail(1))->get('/login');
        $response->assertRedirect('/');
    }

    
}
