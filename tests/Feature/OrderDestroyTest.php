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

    protected $url = '/order/destroy/';
    protected $ordersCount;
    protected $singleUserOrder;
    protected $multipleUsersOrder;
    protected $admin;
    protected $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->singleUserOrder = Order::findOrFail(1)->with('item')->first();
        $this->multipleUsersOrder = Order::findOrFail(2)->with('item')->first();
        $this->admin = User::findOrFail(1);
        $this->user = User::findOrFail(2);
        $this->ordersCount = Order::count();
    }

    public function testNotAuthtedUserCanNotDestroyOrder(): void
    {
        $response = $this->get($this->url . $this->getOrderUserPivot($order = $this->multipleUsersOrder)->id);
        $this->isUsersCountNotChanged($order);
        $response->assertRedirect('/login');
    }

    public function testNotInOrderUserCanNotDestroyOrder(): void
    {
        $this->assertNotEquals(($pivot = $this->getOrderUserPivot($order = $this->multipleUsersOrder))->user_id, $this->user->id);
        $response = $this->actingAs($this->user)
            ->get($this->url . $pivot->id);
        $this->isUsersCountNotChanged($order);
        $response->assertRedirect('/');
    }

    public function testUserCanDestroySelfSingleUserOrder(): void
    {
        $pivot = ($order = $this->singleUserOrder)->users->first()->pivot;
        $response = $this->actingAs(User::findOrFail($pivot->user_id))
            ->get($this->url . $pivot->id);
        $this->assertFalse(Order::all()->pluck('id')->contains($order->id));
        $this->assertFalse(Item::all()->pluck('id')->contains($order->item->id));
        // $this->assertEquals($this->getItemsCount() - 1, $this->getActualItemsCount());
        $response->assertRedirect('/');
    }

    public function testUserCanDestroySelfMultipleUserOrder(): void
    {
        $pivot = ($order = $this->multipleUsersOrder)->users->first()->pivot;
        dd($order->users);
        $response = $this->actingAs(User::findOrFail($pivot->user_id))
            ->get($this->url . $pivot->id);
        $this->assertCount($order->users->count() -1, Order::findOrFail($order->id)->users->count());
        $this->assertTrue(Order::all()->pluck('id')->contains($order->id));
        $this->assertTrue(Item::all()->pluck('id')->contains($order->item->id));
        $response->assertRedirect('/');
    }

    public function testAdminUserCanDestroySingleUserOrder(): void
    {
        $pivot = ($order = $this->singleUserOrder)->users->first()->pivot;
        $response = $this->actingAs($this->admin)
            ->get($this->url . $pivot->id);
        $this->assertFalse(Order::all()->pluck('id')->contains($order->id));
        $this->assertFalse(Item::all()->pluck('id')->contains($order->item->id));
        $response->assertRedirect('/');
    }

    public function testAdminUserCanDestroyMultipleUserOrder(): void
    {
        $pivot = ($order = $this->multipleUsersOrder)->users->first()->pivot;
        $response = $this->actingAs($this->admin)
            ->get($this->url . $pivot->id);
        $this->assertCount($order->users->count() -1, Order::findOrFail($order->id)->users->count());
        $this->assertTrue(Order::all()->pluck('id')->contains($order->id));
        $this->assertTrue(Item::all()->pluck('id')->contains($order->item->id));
        $response->assertRedirect('/');
    }

    public function testAdminUserCanNotDestroyArchivedOrder(): void
    {
        $group = $this->groupToArchive(Group::first());
        $order = $group->orders->first();
        $pivot = $order->users->first()->pivot;
        $response = $this->actingAs($this->admin)
            ->get($this->url . $pivot->id);
        $this->assertCount($order->users->count(), Order::findOrFail($order->id)->users->count());
        $this->assertTrue(Order::all()->pluck('id')->contains($order->id));
        $this->assertTrue(Item::all()->pluck('id')->contains($order->item->id));
        $response->assertRedirect('/');
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
    }

}