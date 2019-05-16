<?php

namespace Tests\Feature\Repositories;

use App\User;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    public $user;

    public function __construct()
    {
        $this->createInstance();
    }

    protected function createInstance(): void
    {
        $this->user = factory(User::class)->make([
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

    public function __destruct()
    {
        $this->user->delete();
    }

}