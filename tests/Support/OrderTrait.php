<?php

namespace Tests\Support;

use App\Group;
// use Ixudra\Curl\Facades\Curl;
// use Illuminate\Database\Eloquent\Collection;

trait OrderTrait
{

    private $group;

    public function orderTrait(): void
    {
        $this->group = Group::first();
    }

    public function getGroup(): Group
    {
        return $this->group;
    }
    
}