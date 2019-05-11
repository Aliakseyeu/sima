<?php

namespace Tests\Feature\Traits;

// use Faker;
// use App\User;
use Illuminate\Support\Facades\Hash;

trait UserTrait
{

    // protected $faker;
    // protected $user;
    // protected $password = 'super-password';

    public function __construct()
    {
        // $this->faker = Faker\Factory::create('ru_RU');
        dd(bcrypt('ok'));
        // $this->createInstance();
    }

    // public function createInstance(): void
    // {
    //     dd(bcrypt($this->password));
    //     $this->user = factory(User::class)->make([
    //             'name' => $this->faker->firstName,
    //             'email' => $this->faker->unique()->safeEmail,
    //             'phone' => $this->faker->phoneNumber,
    //             'surname' => $this->faker->lastName,
    //             'password' => $this->password,
    //         ]
    //     );
    //     dd($this->user);
    // }

    // public function __destruct()
    // {
    //     $this->user->delete();
    // }

}