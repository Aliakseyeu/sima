<?php

namespace App\Objects\Report;

use App\User;
use Illuminate\Support\Collection;

class Report
{

    protected $users;

    public function __construct()
    {
        $this->users = collect();
    }

    public function addUser(User $user): void
    {
        if (!$this->users->has($user->id)) {
            $this->users->put($user->id, $user);
        }
    }

    public function getUsers(): Collection
    {
        return $this->users;
    }

}
