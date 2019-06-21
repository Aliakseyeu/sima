<?php

namespace Tests\Feature;

use App\{Group, Status, User};
use App\Exceptions\{NotAuthorizedException, NotEmptyException};
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Tests\Support\{Prepare, GroupTrait, UserTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;

class GroupTest extends Prepare
{

    use GroupTrait;
    use UserTrait;

    protected $count;

    public function setUp(): void
    {
        parent::setUp();
        $this->count = Group::count();
    }

    public function testUserCanNotStoreGroup(): void
    {
        $response = $this->storeQuery($this->getUser());
        $response->assertRedirect('/');
        $this->compareGroupsCount();
        $this->assertTrue($response->exception instanceof NotAuthorizedException);
    }

    public function testAdminCanStoreGroup(): void
    {
        $response = $this->storeQuery($this->getAdmin());
        $response->assertRedirect('/');
        $this->compareGroupsCount(0, 1);
    }

    public function testAdminCanNotDestroyNotEmptyGroup(): void
    {
        $this->isNotEmptyGroup($group = $this->getGroup());
        $response = $this->destroyQuery($this->getAdmin(), $group);
        $response->assertRedirect('/');
        $this->compareGroupsCount();
        $this->assertTrue($response->exception instanceof NotEmptyException);
    }

    public function testAdminCanDestroyEmptyGroup(): void
    {
        $this->isEmptyGroup($group = $this->getEmptyGroup());
        $response = $this->destroyQuery($this->getAdmin(), $group);
        $response->assertRedirect('/');
        $this->compareGroupsCount(1);
    }

    public function testUserCanNotDestroyEmptyGroup(): void
    {
        $this->isEmptyGroup($group = $this->getEmptyGroup());
        $response = $this->destroyQuery($this->getUser(), $group);
        $response->assertRedirect('/');
        $this->compareGroupsCount();
        $this->assertTrue($response->exception instanceof NotAuthorizedException);
    }

    public function testAdminCanNotDestroyArchivedGroup(): void
    {
        $group = $this->groupToArchive($this->getEmptyGroup());
        $response = $this->destroyQuery($this->getAdmin(), $group);
        $response->assertRedirect('/');
        $this->compareGroupsCount();
        $this->assertTrue($response->exception instanceof ModelNotFoundException);
    }

    public function testUserCanNotArchiveGroup(): void
    {
        $this->checkGroupStatus($group = $this->getEmptyGroup());
        $response = $this->archiveQuery($this->getUser(), $group);
        $response->assertRedirect('/');
        $this->checkGroupStatus($group);
        $this->assertTrue($response->exception instanceof NotAuthorizedException);
    }

    public function testAdminCanArchiveGroup(): void
    {
        $this->checkGroupStatus($group = $this->getEmptyGroup());
        $response = $this->archiveQuery($this->getAdmin(), $group);
        $response->assertRedirect('/');
        $this->checkGroupStatus($group, 'archived');
    }

    public function testUserCanNotPutGroupToCart(): void
    {
        // $this->isNotEmptyGroup($group = $this->getGroup());
        // $response = $this->toCartQuery($this->getUser(), $group);
        // $response->assertRedirect('/');
        // $this->assertTrue($response->exception instanceof NotAuthorizedException);
    }

    public function testAdminCanNotPutEmptyGroupToCart(): void
    {
        
    }

    public function testAdminCanPutGroupToCart(): void
    {
        
    }

    protected function storeQuery(User $user): Response
    {
        return $this->actingAs($user)->get('/group/store');
    }

    protected function destroyQuery(User $user, Group $group): Response
    {
        return $this->actingAs($user)->get('/group/destroy/' . $group->id);
    }

    protected function archiveQuery(User $user, Group $group): Response
    {
        return $this->actingAs($user)->get('/archive/store/' . $group->id);
    }

    protected function toCartQuery(User $user, Group $group): Response
    {
        return $this->actingAs($user)->get('/group/toCart/' . $group->id);
    }

    protected function compareGroupsCount(int $sub = 0, int $add = 0): void
    {
        $this->assertEquals($this->count - $sub + $add, Group::count());
    }

    protected function checkGroupStatus(Group $group, string $status = 'new'): void
    {
        $this->assertEquals(Status::whereSlug($status)->first()->id, Group::findOrFail($group->id)->status_id);
    }
    
}