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

    public function getEmptyGroup(): Group
    {
        return Group::findOrFail(2);
    }

    public function isEmptyGroup(Group $group): void
    {
        $this->assertTrue($group->orders->count() == 0);
    }

    public function isNotEmptyGroup(Group $group): void
    {
        $this->assertTrue($group->orders->count() > 0);
    }
    
}