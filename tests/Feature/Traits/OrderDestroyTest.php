<?php

namespace Tests\Feature;

use App\{Group, Item, Order, User};
use Tests\Support\{Prepare, GroupTrait, ItemTrait, OrderTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;
use Illuminate\Support\Collection;

class OrderDestroyTest extends Prepare
{

    use GroupTrait;
    use OrderTrait;

    protected $destroyUrl = '/order/destroy/';
    protected $singleOrder;
    protected $multipleOrder;
    protected $admin;
    protected $user;
    // protected $itemsCount;
    // protected $ordersCount;

    public function setUp(): void
    {
        parent::setUp();
        $this->singleOrder = Order::findOrFail(1);
        $this->multipleOrder = Order::findOrFail(2);
        $this->admin = User::findOrFail(1);
        $this->user = User::findOrFail(2);
        // $this->itemsCount = Item::count();
        // $this->ordersCount = Order::count();
    }

    public function testNotAuthtedUserCanNotDestroyOrder(): void
    {
        $response = $this->get($this->destroyUrl . $this->getOrderUserPivot($order = $this->singleOrder)->id);
        $this->isUsersCountNotChanged($order);
        $response->assertRedirect('/login');
    }

    public function testNotInOrderUserCanNotDestroyOrder(): void
    {
        $pivot = $this->getOrderUserPivot($order = $this->singleOrder);
        $this->assertNotEquals($pivot->user_id, $this->user->id);
        $response = $this->actingAs($this->user)
            ->get($this->destroyUrl.$pivot->id);
        $this->isUsersCountNotChanged($order);
        $response->assertRedirect('/');
    }

    public function testUserCanDestroySelfSingleUserOrder(): void
    {
        $pivot = ($order = $this->singleOrder)->users->first()->pivot;
        // dd($order);
        $response = $this->actingAs(User::findOrFail($pivot->user_id))
            ->get($this->destroyUrl . $pivot->id);
            // dd($order->item->id, Item::all()->pluck('id'));
        
        $this->assertFalse(Order::all()->pluck('id')->contains($order->id));
        $this->assertFalse(Item::all()->pluck('id')->contains($order->item->id));//
        $response->assertRedirect('/');
    }

    public function testUserCanDestroySelfMultipleUserOrder(): void
    {
        $pivot = ($order = $this->multipleOrder)->users->first()->pivot;
        $response = $this->actingAs(User::findOrFail($pivot->user_id))
            ->get($this->destroyUrl . $pivot->id);
        $this->assertCount($order->users->count() - 1, Order::findOrFail($order->id)->users);
        $this->assertTrue(Item::all()->pluck('id')->contains($order->item->id));//
        $response->assertRedirect('/');
    }

    public function testAdminUserCanDestroySingleUserOrder(): void
    {
        $pivot = ($order = $this->singleOrder)->users->first()->pivot;
        $response = $this->actingAs($this->admin)
            ->get($this->destroyUrl . $pivot->id);
        $this->assertFalse(Order::all()->pluck('id')->contains($order->id));
        $this->assertFalse(Item::all()->pluck('id')->contains($order->item->id));//
        $response->assertRedirect('/');
    }

    public function testAdminUserCanDestroyMultipleUserOrder(): void
    {
        $pivot = ($order = $this->multipleOrder)->users->first()->pivot;
        $response = $this->actingAs($this->admin)
            ->get($this->destroyUrl . $pivot->id);
        $this->assertCount($order->users->count() - 1, Order::findOrFail($order->id)->users);
        $this->assertTrue(Item::all()->pluck('id')->contains($order->item->id));//
        $response->assertRedirect('/');
    }

    public function testAdminUserCanNotDestroyArchivedOrder(): void
    {
        $this->groupToArchive($group = Group::first());
        $order = $group->orders->first();
        $pivot = $order->users->first()->pivot;
        $response = $this->actingAs($this->admin)
            ->get($this->destroyUrl . $pivot->id);
        $this->assertFalse(Order::all()->pluck('id')->contains($order->id));
        $this->assertFalse(Item::all()->pluck('id')->contains($order->item->id));//
        $this->assertCount($order->users->count(), Order::findOrFail($order->id)->users);
        $response->assertRedirect('/');
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
    }

}