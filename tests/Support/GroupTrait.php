<?php

namespace Tests\Support;

use App\Group;

trait GroupTrait
{

    private $group;

    public function groupTrait(): void
    {
        $this->group = Group::first();
    }

    public function getGroup(): Group
    {
        return $this->group;
    }
    
}