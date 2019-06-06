<?php

namespace Tests\Feature;

use App\{Group, Item, Order, User};
use Tests\Support\{Prepare, GroupTrait, ItemTrait, OrderTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;
use Illuminate\Support\Collection;

class OrderStoreTest extends Prepare
{

    use GroupTrait;
    use ItemTrait;
    use OrderTrait;
    
    protected $existsUrl = '/order/store_exists';
    protected $newUrl = '/order/store_new';
    protected $group;
    protected $order;
    protected $user;
    protected $ordersCount;

    public function setUp(): void
    {
        parent::setUp();
        $this->group = Group::first();
        $this->order = Order::first();
        $this->user = User::findOrFail(1);
        $this->ordersCount = Order::count();
    }

    public function testUserCanStoreExistentItem(): void
    {
        $this->userCanStoreExistentItem($this->showUrl);
    }

    public function testUserCanStoreExistentItemFromIndexPage(): void
    {
        $this->userCanStoreExistentItem('/');
    }

    protected function userCanStoreExistentItem(string $url): void
    {
        $this->assertTrue($this->isUserNotInOrder($this->order, $this->user));
        $response = $this->actingAs($this->user)
            ->from($url)
            ->followingRedirects()
            ->post($this->existsUrl, [
                'id' => $this->order->id,
                'qty' => $qty = $this->getQty()
            ]);
        $response->assertOk();
        $response->assertSee('Заказ успешно создан(а)');
        $this->assertCount($this->order->users->count() + 1, ($order = Order::first())->users);
        $this->assertEquals($qty, $order->users()->whereUserId($this->user->id)->first()->pivot->qty);
    }

    public function testUserCanStoreNewOrder(): void
    {
        $itemsCount = Item::count();
        $item = $this->getActualItem();
        $response = $this->actingAs($this->user)
            ->followingRedirects()
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'id' => $item->id,
                'group' => $this->group->id,
                'qty' => $qty = $this->getQty()
            ]);
        $response->assertOk();
        $response->assertSee('Заказ успешно создан(а)');
        $this->assertEquals($this->ordersCount + 1, Order::count());
        $this->assertEquals($itemsCount + 1, Item::count());
        $this->assertCount(1, ($order = Order::orderBy('id', 'desc')->first())->users);
        $this->assertEquals($this->user->id, $order->users->first()->id);
        $this->assertEquals($item->name, $order->item->name);
        $this->assertEquals($item->id, $order->item->pid);
        $this->assertEquals($item->sid, $order->item->sid);
    }

    public function testUserCanNotStoreExistentItemWithoutId(): void
    {
        $this->assertTrue($this->isUserNotInOrder($this->order, $this->user));
        $response = $this->actingAs($this->user)
            ->from($this->showUrl)
            ->post($this->existsUrl, [
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect($this->showUrl);
        $this->isUsersCountNotChanged($this->order);
        $response->assertSessionHasErrors('id');
    }

    public function testUserCanNotStoreExistentItemWithoutQty(): void
    {
        $this->assertTrue($this->isUserNotInOrder($this->order, $this->user));
        $response = $this->actingAs($this->user)
            ->from($this->showUrl)
            ->post($this->existsUrl, [
                'id' => $this->order->id,
            ]);
        $response->assertRedirect($this->showUrl);
        $this->isUsersCountNotChanged($this->order);
        $response->assertSessionHasErrors('qty');
    }

    public function testUserCanNotStoreExistentItemWithWrongId(): void
    {
        $this->assertTrue($this->isUserNotInOrder($this->order, $this->user));
        $response = $this->actingAs($this->user)
            ->from($this->showUrl)
            ->post($this->existsUrl, [
                'id' => 0,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->isUsersCountNotChanged($this->order);
        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
    }

    public function testUserCanNotStoreNewItemWithoutId(): void
    {
        $response = $this->actingAs($this->user)
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'group' => $this->group->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect($this->showUrl);
        $this->assertCount($this->ordersCount, Order::all());
        $response->assertSessionHasErrors('id');
    }

    public function testUserCanNotStoreNewItemWithWrongId(): void
    {
        $response = $this->actingAs($this->user)
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'id' => 0,
                'group' => $this->group->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->assertCount($this->ordersCount, Order::all());
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
    }

    public function testUserCanNotStoreNewItemWithoutGroup(): void
    {
        $response = $this->actingAs($this->user)
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'id' => $this->getActualItem()->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect($this->showUrl);
        $this->assertCount($this->ordersCount, Order::all());
        $response->assertSessionHasErrors('group');
    }

    public function testUserCanNotStoreNewItemWithoutQty(): void
    {
        $response = $this->actingAs($this->user)
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'id' => $this->getActualItem()->id,
                'group' => $this->group->id,
            ]);
        $response->assertRedirect($this->showUrl);
        $this->assertCount($this->ordersCount, Order::all());
        $response->assertSessionHasErrors('qty');
    }

    public function testUserCanNotStoreNewItemWithWrongGroup(): void
    {
        $response = $this->actingAs($this->user)
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'id' => $this->getActualItem()->id,
                'group' => 0,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->assertCount($this->ordersCount, Order::all());
        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
    }

    public function testUserCanNotStoreNewItemInArchivedGroup(): void
    {
        $this->groupToArchive();
        $response = $this->actingAs($this->user)
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'id' => $this->getActualItem()->id,
                'group' => $this->group->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->assertCount($this->ordersCount, Order::all());
        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
    }

    public function testUserCanNotStoreOrderTwice(): void
    {
        $response = $this->actingAs($user = $this->order->users->first())
            ->post($this->existsUrl, [
                'id' => $this->order->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->assertTrue($response->exception instanceof \App\Exceptions\Order\OrderNotCreatedException);
        $this->isUsersCountNotChanged($this->order);
        $this->assertEquals(
            $this->getOrderUserPivot($this->order)->qty, 
            $this->getOrderUserPivot(Order::findOrFail($this->order->id))->qty
        );
    }

    /*public function testUserCanUpdateSelfOrder(): void
    {
        $response = $this->actingAs($user = $this->getOrder()->users->first())
            ->followingRedirects()
            ->post($this->updateUrl, [
                'id' => ($pivot = $this->getOrderUserPivot())->id,
                'qty' => $pivot->qty + 1
            ]);
        $response->assertOk();
        $response->assertSee('Заказ успешно обновлен(а)');
        $this->assertEquals($pivot->qty + 1, $this->getNewOrderUserPivot()->qty);
    }

    /*public function testAdminCanUpdateAnyOrder(): void
    {
        $this->isUserNotInOrder($this->getAdmin());
        $response = $this->actingAs($user = $this->getAdmin())
            ->followingRedirects()
            ->post($this->updateUrl, [
                'id' => ($pivot = $this->getOrderUserPivot())->id,
                'qty' => $pivot->qty + 1
            ]);
        $response->assertOk();
        $response->assertSee('Заказ успешно обновлен(а)');
        $this->assertEquals($pivot->qty + 1, $this->getNewOrderUserPivot()->qty);
    }

    protected function userCanUpdateOrder(): void
    {
        
    }*/

    /*public function testUserCanNotUpdateAnotherUsersOrder(): void
    {

    }

    public function testUserCanNotUpdateSelfOrderWithoutId(): void
    {

    }

    public function testUserCanNotUpdateSelfOrderWithoutQty(): void
    {

    }*/

    // protected function isOrdersCountNotChanged(): void
    // {
    //     $this->assertEquals($this->getOrdersCount(), $this->getActualOrdersCount());
    // }

    // protected function isNoNewUsersIsOrder(): void
    // {
    //     $this->assertCount($this->getOrderUsersCount(), $this->getNewOrderUsers());
    // }

    // protected function isUserNotInOrder(\App\User $user = null): bool
    // {
    //     return !$this->getItem()->order->users->pluck('id')->contains(($user ?? $this->getUser())->id);
    // }

    // protected function getOrderUsersCount(): int
    // {
    //     return $this->getItem()->order->users->count();
    // }

    // protected function getNewOrderUsers(): Collection
    // {
    //     return $this->getItemById($this->getItem()->id)->order->users;
    // }

    // protected function getOrderUserPivot(): object
    // {
    //     return $this->getOrder()->users->first()->pivot;
    // }

    // protected function getNewOrderUserPivot(): object
    // {
    //     return $this->getOrderById($this->getOrder()->id)->users->first()->pivot;
    // }

}