<?php

namespace Tests\Feature;

use App\{Group, Item, Order, User};
use Tests\Support\{Prepare, GroupTrait, ItemTrait, OrderTrait, UserTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;
use Illuminate\Support\Collection;

class OrderDestroyTest extends Prepare
{

    use GroupTrait;
    use OrderTrait;
    use UserTrait;

    protected $url = '/order/destroy/';
    protected $ordersCount;

    public function setUp(): void
    {
        parent::setUp();
        $this->ordersCount = Order::count();
    }

    public function testNotAuthtedUserCanNotDestroyOrder(): void
    {
        $response = $this->get($this->url . $this->getOrderUserPivot($order = $this->getMultipleUsersOrder())->id);
        $this->isUsersCountNotChanged($order);
        $response->assertRedirect('/login');
    }

    public function testNotInOrderUserCanNotDestroyOrder(): void
    {
        $this->assertNotEquals(
            ($pivot = $this->getOrderUserPivot($order = $this->getMultipleUsersOrder()))->user_id, 
            ($user = $this->getUser())->id
        );
        $response = $this->sendQuery($user, $pivot);
        $this->isUsersCountNotChanged($order);
        $response->assertRedirect('/');
    }

    public function testUserCanDestroySelfSingleUserOrder(): void
    {
        $pivot = ($order = $this->getSingleUserOrder())->users->first()->pivot;
        $response = $this->sendQuery(User::findOrFail($pivot->user_id), $pivot);
        $this->assertTrue($this->isOrderDeleted($order));
        $response->assertRedirect('/');
    }

    public function testUserCanDestroySelfMultipleUserOrder(): void
    {
        $pivot = ($order = $this->getMultipleUsersOrder())->users->first()->pivot;
        $response = $this->sendQuery(User::findOrFail($pivot->user_id), $pivot);
        $this->isUsersCountNotChanged($order, 1);
        $this->assertTrue($this->isOrderNotDeleted($order));
        $response->assertRedirect('/');
    }

    public function testAdminUserCanDestroySingleUserOrder(): void
    {
        $pivot = ($order = $this->getSingleUserOrder())->users->first()->pivot;
        $response = $this->sendQuery($this->getAdmin(), $pivot);
        $this->assertTrue($this->isOrderDeleted($order));
        $response->assertRedirect('/');
    }

    public function testAdminUserCanDestroyMultipleUserOrder(): void
    {
        $pivot = ($order = $this->getMultipleUsersOrder())->users->first()->pivot;
        $response = $this->sendQuery($this->getAdmin(), $pivot);
        $this->isUsersCountNotChanged($order, 1);
        $this->assertTrue($this->isOrderNotDeleted($order));
        $response->assertRedirect('/');
    }

    public function testAdminUserCanNotDestroyArchivedOrder(): void
    {
        $group = $this->groupToArchive(Group::first());
        $order = $group->orders->first();
        $pivot = $order->users->first()->pivot;
        $response = $this->sendQuery($this->getAdmin(), $pivot);
        $this->isUsersCountNotChanged($order);
        $this->assertTrue($this->isOrderNotDeleted($order));
        $response->assertRedirect('/');
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
    }

    protected function sendQuery(User $user, object $pivot): Response
    {
        return $response = $this->actingAs($user)->get($this->url . $pivot->id);
    }

}