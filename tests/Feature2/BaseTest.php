<?php

namespace Tests\Feature;

use App\{Role, User};
use Tests\TestCase;
use Illuminate\Support\Facades\Hash;

class BaseTest extends TestCase
{

    public function testTrue(): void
    {
        $this->assertTrue(true);
    }

    protected function authenticateByNew(): self
    {
        $user = factory(User::class)->make();
        return $this->actingAs($user);
    }

    protected function authenticateByTest(): self
    {
        return $this->actingAs($this->getTestUser()->first());
    }

    protected function getTestUser(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->getUser(env('USER_EMAIL'));
    }

    protected function getRegUser(): \Illuminate\Database\Eloquent\Builder
    {
        return $this->getUser(env('REG_EMAIL'));
    }

    protected function getUser($email): \Illuminate\Database\Eloquent\Builder
    {
        return User::where('email', $email);
    }

    protected function getOrdinaryRoleUser(): User
    {
        return Role::whereSlug('user')->first()->users->first();
    }

    protected function getAdminRoleUser(): User
    {
        return Role::whereSlug('admin')->first()->users->first();
    }
    
}
