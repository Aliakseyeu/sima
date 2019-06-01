<?php

namespace Tests\Feature;

use Tests\Support\Prepare;
use Tests\Support\UserTrait;

class LoginTest extends Prepare
{

    use UserTrait;

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
                'email' => $this->getUser()->email.'asd', 
                'password' => $this->getPassword()
            ]);
        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    public function testUserCanNotLoginWithWrongPassword(): void
    {
        $response = $this->from('/login')
            ->post('/login', [
                'email' => $this->getUser()->email, 
                'password' => 'wrong-pass'
            ]);
        $response->assertRedirect('/login');
        $this->assertGuest();        
    }

    public function testUserCanLoginWithCorrectData(): void
    {
        $response = $this->post('/login', [
            'email' => $this->getUser()->email,
            'password' => $this->getPassword(),
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($this->getUser());
    }

    public function testAuthenticatedUserCanNotSeeLoginPage():void
    {
        $response = $this->actingAs($this->getUser())->get('/login');
        $response->assertRedirect('/');
    }

    
}
