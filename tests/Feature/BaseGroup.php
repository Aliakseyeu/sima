<?php

namespace Tests\Feature;

use App\{
    Group,
    Status
};
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class BaseGroup extends BaseUser
{
	
	use RefreshDatabase;
	// use DatabaseMigrations;
	
	private $group;
	
	public function setUp(): void
	{
		parent::setUp();
//		$this->group = new Group();
//		$this->group->save();
        $this->group = Group::first();
	}
	
	public function getGroup(): Group
	{
		return $this->group;
	}
	
//	public function tearDown(): void
//    {
//		parent::tearDown();
//		$this->group->truncate();
//	}
//	
//	
//	
//	
//
//    public function getActualGroup(): Group
//    {
//        return (new Status)->new()->groups->first();
//    }
//
//    public function getLastArchivedGroups(): Collection
//    {
//        return (new Status)->archived()->groups->sortByDesc('id');
//    }
//
//    public function getEmptyGroup(): Group
//    {
//        return new Group();
//    }
    
}
