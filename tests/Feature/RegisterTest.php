<?php

namespace Tests\Feature;

use Hash;
use App\User;
use Tests\Support\{Prepare, UserTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;

class RegisterTest extends Prepare
{

    use UserTrait;

    protected $registerUrl = '/register';
    protected $count;

    public function setUp():void
    {
        parent::setUp();
        $this->count = User::count();
    }

    public function testIsRegisterPageAvailable(): void
    {
        $response = $this->get('/register');
        $response->assertOk();
    }

    public function testUserCanRegister(): void
    {
        ($user = $this->getUser())->delete();
        $response = $this->post($this->registerUrl, $data = $this->getRegisterData($user));
        $response->assertRedirect('/');
        $this->assertEquals($this->count, User::count());
        $this->assertAuthenticatedAs($user = User::orderBy('id', 'desc')->first());
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
        $response = $this->from($this->registerUrl)
            ->post($this->registerUrl, $this->mergeRegisterData(['email' => $this->getUser()->email]));
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
            ->post($this->registerUrl, $this->mergeRegisterData(['password_confirmation' => env('PASSWORD').'asd']));
        $this->isUserNotRegistered($response, 'password');
    }

    protected function mergeRegisterData(array $data): array
    {
        return array_merge($this->getRegisterData(), $data);
    }

    protected function isUserNotRegistered(Response $response, string $errorKey): void
    {
        $response->assertRedirect($this->registerUrl);
        $this->assertEquals($this->count, User::count());
        $response->assertSessionHasErrors($errorKey);
        $this->assertGuest();
    }

    protected function getRegisterData(User $user = null): array
    {
        return array_merge(
            ($user ?? $this->getUser())->toArray(),
            [
                'password' => env('PASSWORD'),
                'password_confirmation' => env('PASSWORD')
            ]
        );
    }

    protected function checkPasswords(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

}
