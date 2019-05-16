<?php

namespace Tests\Feature;

use App\{
    Group,
    Status
};
use Illuminate\Database\Eloquent\Collection;

class BaseGroup extends BaseUser
{
	
	private $group;
	
	public function setUp(): void
	{
		parent::setUp();
		$this->group = new Group();
		$this->group->save();
	}
	
	public function getGroup(): Group
	{
		return $this->group;
	}
	
	public function __destruct()
	{
		$this->group->delete();
	}
	
	
	
	

    public function getActualGroup(): Group
    {
        return (new Status)->new()->groups->first();
    }

    public function getLastArchivedGroups(): Collection
    {
        return (new Status)->archived()->groups->sortByDesc('id');
    }

    public function getEmptyGroup(): Group
    {
        return new Group();
    }
    
}
