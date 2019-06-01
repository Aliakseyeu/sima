<?php

namespace tests\Feature\Traits;

use App\User;
 
trait UserTrait
{

//    private $admin;
//    private $user;
//    private $count;

    public function userTrait()
    {
        $this->admin = User::findOrFail(1);
        $this->user = User::findOrFail(2);
        $this->count = $this->getUsersCount();
    }

    public function getUsersCount(): int
    {
        return User::count();
    }

    public function getPassword(): string
    {
        return env('PASSWORD');
    }

    public function getAdmin(): User
    {
        return $this->admin;
    }

    public function getUser(): User
    {
        return User::findOrFail(2);
        return $this->user;
    }

    public function getUCount(): int
    {
        return $this->count;
    }
}
