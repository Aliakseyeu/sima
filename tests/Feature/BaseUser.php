<?php

namespace Tests\Feature;

use Tests\TestCase;

use Faker;
use App\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BaseUser extends TestCase
{
	
	use RefreshDatabase;
	use DatabaseMigrations;

    private $user;
    private $faker;
    private $password = 'super-password';

    public function setUp()
    {
    	parent::setUp();
    	$this->artisan('db:seed', ['--env'=>'testing']);
        $this->faker = Faker\Factory::create('ru_RU');
        $this->user = $this->createUser();
    }

    public function createUser(): User
    {
        return factory(User::class)->create([
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
    	$this->user->delete();
        return array_merge(
            $this->user->toArray(),
            [
                'password' => $this->password,
                'password_confirmation' => $this->password
            ]
        );
    }

    public function findUsersByEmail(): Collection
    {
        return User::whereEmail($this->user->email)->get();
    }

    public function checkPasswords(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    public function storeUser(): self
    {
        $this->user->save();
        return $this;
    }

    public function getUsersCount(): int
    {
        return User::count();
    }

//    public function getRandomUser(): User
//    {
//        return User::inRandomOrder()->first();
//    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getPassword(): string
    {
        return $this->password;
    }
    
//    public function tearDown(): void
//    {
//		parent::tearDown();
//		$this->user->truncate();
//	}

}