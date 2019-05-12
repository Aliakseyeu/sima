<?php

namespace Tests\Feature;

use Faker;
use App\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    protected $user;
    protected $faker;
    protected $password = 'super-password';

    public function __construct()
    {
        $this->faker = Faker\Factory::create('ru_RU');
        $this->createInstance();
    }

    protected function createInstance(): void
    {
        $this->user = factory(User::class)->create([
                'name' => $this->faker->firstName,
                'email' => $this->faker->unique()->safeEmail,
                'phone' => $this->faker->phoneNumber,
                'surname' => $this->faker->lastName,
                'password' => $this->password,
            ]
        );
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function __destruct()
    {
        $this->user->delete();
    }

}