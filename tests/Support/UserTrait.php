<?php

namespace Tests\Support;

use Hash;
use App\User;

trait UserTrait
{

    private $admin;
    private $user;


    // public function getRegisterData(): array
    // {
    //     return array_merge(
    //         $this->user->toArray(),
    //         [
    //             'password' => $this->getPassword(),
    //             'password_confirmation' => $this->getPassword()
    //         ]
    //     );
    // }



    public function getAdmin(): User
    {
        return User::findOrFail(1);
    }

    public function getUser(int $id = 2): User
    {
        return User::findOrFail($id);
    }


}