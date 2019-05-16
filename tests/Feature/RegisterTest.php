<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Foundation\Testing\TestResponse as Response;

class RegisterTest extends TestCase
{

    protected $userRepository;
    protected $count;
    protected $registerUrl = '/register';

    public function setUp()
    {
        parent::setUp();
        $this->userRepository = new UserRepository();
        $this->count = $this->userRepository->getCount();
    }

    public function testIsRegisterPageAvailable(): void
    {
        $response = $this->get('/register');
        $response->assertOk();
    }

    public function testUserCanRegister(): void
    {
        $response = $this->post($this->registerUrl, $data = $this->userRepository->getRegisterData());
        $response->assertRedirect('/');
        $users = $this->userRepository->findUserByEmail();
        $this->assertEquals($this->count + 1, $this->userRepository->getCount());
        $this->assertAuthenticatedAs($user = $users->first());
        $this->assertEquals($data['name'], $user->name);
        $this->assertEquals($data['surname'], $user->surname);
        $this->assertEquals($data['email'], $user->email);
        $this->assertEquals($data['phone'], $user->phone);
        $this->assertTrue($this->userRepository->checkPasswords($data['password'], $user->password));
    }

    public function testUserCanNotRegisterWithoutName(): void
    {
        $response = $this->from($this->registerUrl)
            ->post($this->registerUrl, $this->mergeRegisterData(['name' => '']));
        $this->isUserNotRegistered($response, 'name');
    }

    public function testUserCanNotRegisterWithRegisteredEmail(): void
    {
        $response = $this->from($this->registerUrl)
            ->post($this->registerUrl, $this->mergeRegisterData(['email' => $this->userRepository->getRandomUser()->email]));
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
            ->post($this->registerUrl, $this->mergeRegisterData(['password_confirmation' => $this->userRepository->getPassword().'asd']));
        $this->isUserNotRegistered($response, 'password');
    }

    protected function mergeRegisterData(array $data): array
    {
        return array_merge($this->userRepository->getRegisterData(), $data);
    }

    protected function isUserNotRegistered(Response $response, string $errorKey): void
    {
        $response->assertRedirect($this->registerUrl);
        $this->assertEquals($this->count, $this->userRepository->getCount());
        $response->assertSessionHasErrors($errorKey);
        $this->assertGuest();
    }

}
