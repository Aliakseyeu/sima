<?php

namespace Tests\Feature\Repositories;

use App\{Role, User};
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    // public function authenticateByNew(): self
    // {
    //     $user = factory(User::class)->make();
    //     return $this->actingAs($user);
    // }

    // public function authenticateByTest(): self
    // {
    //     return $this->actingAs($this->getTestUser()->first());
    // }

    public function getNewUser(): User
    {
        return factory(User::class)->make();
    }

    public function getTestUser(): User
    {
        return $this->getTestUserBuilder()->first();
    }

    public function getTestUserBuilder(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->getUser(env('TEST_USER_EMAIL'));
    }

    public function getRegUserBuilder(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->getUser(env('REG_USER_EMAIL'));
    }

    protected function getUser($email): \Illuminate\Database\Eloquent\Builder
    {
        return User::where('email', $email);
    }

    public function getOrdinaryRoleUser(): User
    {
        return Role::whereSlug('user')->first()->users->first();
    }

    public function getAdminRoleUser(): User
    {
        return Role::whereSlug('admin')->first()->users->first();
    }
    
}
