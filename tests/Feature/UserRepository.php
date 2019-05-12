<?php

namespace Tests\Feature;

use Faker;
use App\User;
use Illuminate\Database\Eloquent\Collection;
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
        $this->user = factory(User::class)->make([
                'name' => $this->faker->firstName,
                'email' => $this->faker->unique()->safeEmail,
                'phone' => $this->faker->phoneNumber,
                'surname' => $this->faker->lastName,
                'password' => Hash::make($this->password),
            ]
        );
    }

    public function getRegisterData(): array
    {
        return array_merge(
            $this->user->toArray(),
            [
                'password' => $this->password,
                'password_confirmation' => $this->password
            ]
        );
    }

    public function findUserByEmail(): Collection
    {
        return User::whereEmail($this->user->email)->get();
    }

    public function checkPasswords(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    public function store(): void
    {
        $this->user->save();
    }

    public function getCount(): int
    {
        return User::count();
    }

    public function getRandomUser(): User
    {
        return User::inRandomOrder()->first();
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