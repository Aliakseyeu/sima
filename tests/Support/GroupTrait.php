<?php

namespace Tests\Support;

use App\{Group, Status};

trait GroupTrait
{

    public function groupToArchive(Group $group): Group
    {
        $group->status()->associate(Status::whereSlug('archived')->first())->save();
        return $group;
    }

    public function getGroup(): Group
    {
        return Group::first();
    }
    
}