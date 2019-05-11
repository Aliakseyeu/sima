<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Tests\Feature\Repositories\UserRepository;
use \Illuminate\Foundation\Testing\TestResponse as Response;

class RegisterTest extends TestCase
{

    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function testIsRegisterPageAvailable(): void
    {
        $response = $this->get('/register');
        $response->assertOk();
    }

    public function testUserCanRegister(): void
    {
        $response = $this->post('/register', $this->userRepository->getRegUserData());
        $response->assertRedirect('/');
        $this->assertCount(1, $users = $this->userRepository->getRegUser()->get());
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
            $this->userRepository->getRegUserData(), 
            ['name' => '']
        ));
        $this->isUserNotRegistered($response, 'name');
    }

    public function testUserCanNotRegisterWithRegisteredEmail(): void
    {
        $response = $this->from('/register')->post('/register', array_merge(
            $this->userRepository->getRegUserData(), 
            ['email' => env('USER_EMAIL')]
        ));
        $this->isUserNotRegistered($response, 'email');
    }

    public function testUserCanNotRegisterWithoutPassword(): void
    {
        $response = $this->from('/register')->post('/register', array_merge(
            $this->userRepository->getRegUserData(), 
            ['password' => '']
        ));
        $this->isUserNotRegistered($response, 'password');
    }

    public function testUserCanNotRegisterWithWrongPasswordConfirm(): void
    {
        $response = $this->from('/register')->post('/register', array_merge(
            $this->userRepository->getRegUserData(), 
            ['password_confirmation' => env('REG_WRONG_PASS')]
        ));
        $this->isUserNotRegistered($response, 'password');
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

    protected function isUserNotRegistered(Response $response, string $errorKey): void
    {
        $response->assertRedirect('/register');
        $this->assertCount(0, $this->userRepository->getRegUser()->get());
        $response->assertSessionHasErrors($errorKey);
        $this->assertGuest();
    }

}
