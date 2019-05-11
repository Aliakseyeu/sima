<?php

namespace Tests\Feature\Repositories;

use App\{
    Group,
    Status
};
use Illuminate\Database\Eloquent\Collection;

class GroupRepository
{

    public function getActualGroup(): Group
    {
        return (new Status)->new()->groups->first();
    }

    public function getLastArchivedGroups(): Collection
    {
        return (new Status)->archived()->groups->sortByDesc('id');
    }
    
}