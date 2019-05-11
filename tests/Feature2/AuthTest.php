<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class AuthTest extends BaseTest
{
    public function testIsLoginPageAvailable(): void
    {
        $response = $this->get('/login');
        $response->assertOk();
    }

    public function testUserCanNotLoginWithIncorrectData(): void
    {
        $response = $this->from('/login')->post('/login', []);
        $response->assertRedirect('/login');
        $this->assertGuest();

        $response = $this->from('/login')->post('/login', ['email' => env('USER_EMAIL'), 'password' => env('USER_WRONG_PASS')]);
        $response->assertRedirect('/login');
        $this->assertGuest();

        $response = $this->from('/login')->post('/login', ['email' => env('USER_WRONG_EMAIL'), 'password' => env('USER_PASS')]);
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
        $this->assertAuthenticatedAs($this->getTestUser()->first());
    }

    public function testAuthenticatedUserCanNotSeeLoginPage():void
    {
        $response = $this->authenticateByNew()->get('/login');
        $response->assertRedirect('/');
    }

    public function testIsRegisterPageAvailable(): void
    {
        $response = $this->get('/register');
        $response->assertOk();
    }

    public function testUserCanRegister(): void
    {
        $response = $this->post('/register', $this->getRegUserData());
        $response->assertRedirect('/');
        $this->assertCount(1, $users = $this->getRegUser()->get());
        $this->assertAuthenticatedAs($user = $users->first());
        $this->assertEquals(env('REG_NAME'), $user->name);
        $this->assertEquals(env('REG_SURNAME'), $user->surname);
        $this->assertEquals(env('REG_EMAIL'), $user->email);
        $this->assertEquals(env('REG_PHONE'), $user->phone);
        $this->assertTrue(Hash::check(env('REG_PASS'), $user->password));
        $user->delete();
    }

    public function testUserCanNotRegisterWithoutName(): void
    {
        $response = $this->from('/register')->post('/register', array_merge(
            $this->getRegUserData(), 
            ['name' => '']
        ));
        $response->assertRedirect('/register');
        $this->assertCount(0, $users = $this->getRegUser()->get());
        $response->assertSessionHasErrors('name');
        $this->assertGuest();
    }

    public function testNotAuthenticatedUserCanNotLogout(): void
    {
        $response = $this->post('/logout');
        $response->assertRedirect('/');
    }

    public function testAuthenticatedUserCanLogout(): void
    {
        $response = $this->authenticateByTest()->post('/logout');
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    public function testUserCanNotRegisterWithRegisteredEmail(): void
    {
        $response = $this->from('/register')->post('/register', array_merge(
            $this->getRegUserData(), 
            ['email' => env('USER_EMAIL')]
        ));
        $response->assertRedirect('/register');
        $this->assertCount(0, $users = $this->getRegUser()->get());
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function testUserCanNotRegisterWithoutPassword(): void
    {
        $response = $this->from('/register')->post('/register', array_merge(
            $this->getRegUserData(), 
            ['password' => '']
        ));
        $response->assertRedirect('/register');
        $this->assertCount(0, $users = $this->getRegUser()->get());
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    public function testUserCanNotRegisterWithWrongPasswordConfirm(): void
    {
        $response = $this->from('/register')->post('/register', array_merge(
            $this->getRegUserData(), 
            ['password_confirmation' => env('REG_WRONG_PASS')]
        ));
        $response->assertRedirect('/register');
        $this->assertCount(0, $users = $this->getRegUser()->get());
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    protected function getRegUserData(): array
    {
        return [
            'name' => env('REG_NAME'),
            'surname' => env('REG_SURNAME'),
            'phone' => env('REG_PHONE'),
            'email' => env('REG_EMAIL'),
            'password' => env('REG_PASS'),
            'password_confirmation' => env('REG_PASS'),
        ];
    }
}
