<?php

namespace Tests\Feature;

use App\User;
use Tests\Support\{Prepare, UserTrait};

class LoginTest extends Prepare
{

    use UserTrait;

    protected $url = '/login';

    public function testIsLoginPageAvailable(): void
    {
        $response = $this->get($this->url);
        $response->assertOk();
    }

    public function testUserCanNotLoginWithEmptyData(): void
    {
        $response = $this->from($this->url)->post($this->url, []);
        $response->assertRedirect($this->url);
        $this->assertGuest();
    }

    public function testUserCanNotLoginWithWrongEmail(): void
    {
        $response = $this->from($this->url)
            ->post($this->url, [
                'email' => $this->getUser()->email.'asd', 
                'password' => env('PASSWORD')
            ]);
        $response->assertRedirect($this->url);
        $this->assertGuest();
    }

    public function testUserCanNotLoginWithWrongPassword(): void
    {
        $response = $this->from($this->url)
            ->post($this->url, [
                'email' => $this->getUser()->email, 
                'password' => 'wrong-pass'
            ]);
        $response->assertRedirect($this->url);
        $this->assertGuest();        
    }

    public function testUserCanLoginWithCorrectData(): void
    {
        $response = $this->post($this->url, [
            'email' => ($user = $this->getUser())->email,
            'password' => env('PASSWORD'),
        ]);
        $response->assertRedirect('/');
        $this->assertAuthenticatedAs($user);
    }

    public function testAuthenticatedUserCanNotSeeLoginPage():void
    {
        $response = $this->actingAs($this->getUser())->get($this->url);
        $response->assertRedirect('/');
    }

    
}
