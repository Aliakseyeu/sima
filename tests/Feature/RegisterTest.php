<?php

namespace Tests\Feature;

use \Illuminate\Foundation\Testing\TestResponse as Response;

class RegisterTest extends BaseUser
{

    protected $registerUrl = '/register';

    public function setUp()
    {
        parent::setUp();
    }

    public function testIsRegisterPageAvailable(): void
    {
        $response = $this->get('/register');
        $response->assertOk();
    }

    public function testUserCanRegister(): void
    {
        $response = $this->post($this->registerUrl, $data = $this->getRegisterData());
        $response->assertRedirect('/');
        $this->assertEquals(1, $this->getUsersCount());
        $this->assertAuthenticatedAs($user = $this->findUsersByEmail()->first());
        $this->assertEquals($data['name'], $user->name);
        $this->assertEquals($data['surname'], $user->surname);
        $this->assertEquals($data['email'], $user->email);
        $this->assertEquals($data['phone'], $user->phone);
        $this->assertTrue($this->checkPasswords($data['password'], $user->password));
    }

    public function testUserCanNotRegisterWithoutName(): void
    {
        $response = $this->from($this->registerUrl)
            ->post($this->registerUrl, $this->mergeRegisterData(['name' => '']));
        $this->isUserNotRegistered($response, 'name');
    }

    public function testUserCanNotRegisterWithRegisteredEmail(): void
    {
    	$registeredUser = $this->createUser();
        $response = $this->from($this->registerUrl)
            ->post($this->registerUrl, $this->mergeRegisterData(['email' => $registeredUser->email]));
        $registeredUser->delete();
        $this->isUserNotRegistered($response, 'email');
    }

    public function testUserCanNotRegisterWithoutPassword(): void
    {
        $response = $this->from($this->registerUrl)
            ->post($this->registerUrl, $this->mergeRegisterData(['password' => '']));
        $this->isUserNotRegistered($response, 'password');
    }

    public function testUserCanNotRegisterWithWrongPasswordConfirm(): void
    {
        $response = $this->from($this->registerUrl)
            ->post($this->registerUrl, $this->mergeRegisterData(['password_confirmation' => $this->getPassword().'asd']));
        $this->isUserNotRegistered($response, 'password');
    }

    protected function mergeRegisterData(array $data): array
    {
        return array_merge($this->getRegisterData(), $data);
    }

    protected function isUserNotRegistered(Response $response, string $errorKey): void
    {
        $response->assertRedirect($this->registerUrl);
        $this->assertEquals(0, $this->getUsersCount());
        $response->assertSessionHasErrors($errorKey);
        $this->assertGuest();
    }

}
