<?php

namespace Tests\Feature\Repositories;

use App\{Role, User};
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class UserRepository
{

    public function authenticateByNew(): self
    {
        $user = factory(User::class)->make();
        return $this->actingAs($user);
    }

    public function authenticateByTest(): self
    {
        return $this->actingAs($this->getTestUser()->first());
    }

    public function getTestUser(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->getUser(env('USER_EMAIL'));
    }

    public function getRegUser(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->getUser(env('REG_EMAIL'));
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
