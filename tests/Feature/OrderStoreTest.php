<?php

namespace Tests\Feature;

use App\{Group, Item, Order, User};
use Tests\Support\{Prepare, GroupTrait, ItemTrait, OrderTrait, UserTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;
use Illuminate\Support\Collection;

class OrderStoreTest extends Prepare
{

    use GroupTrait;
    use ItemTrait;
    use OrderTrait;
    use UserTrait;
    
    protected $existsUrl = '/order/store_exists';
    protected $newUrl = '/order/store_new';
    protected $ordersCount;

    public function setUp(): void
    {
        parent::setUp();
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
        $this->assertTrue($this->isUserNotInOrder($order = $this->getOrder(), $user = $this->getUser()));
        $response = $this->actingAs($user)
            ->from($url)
            ->followingRedirects()
            ->post($this->existsUrl, [
                'id' => $order->id,
                'qty' => $qty = $this->getQty()
            ]);
        $response->assertOk();
        $response->assertSee('Заказ успешно создан(а)');
        $this->assertCount($order->users->count() + 1, ($order = Order::first())->users);
        $this->assertEquals($qty, $order->users()->whereUserId($user->id)->first()->pivot->qty);
    }

    public function testUserCanStoreNewOrder(): void
    {
        $itemsCount = Item::count();
        $item = $this->getActualItem();
        $response = $this->actingAs($user = $this->getUser())
            ->followingRedirects()
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'id' => $item->id,
                'group' => $this->getGroup()->id,
                'qty' => $qty = $this->getQty()
            ]);
        $response->assertOk();
        $response->assertSee('Заказ успешно создан(а)');
        $this->assertEquals($this->ordersCount + 1, Order::count());
        $this->assertEquals($itemsCount + 1, Item::count());
        $this->assertTrue($this->isOrderNotDeleted($order = Order::orderBy('id', 'desc')->first()));
        $this->assertCount(1, $order->users);
        $this->assertEquals($user->id, $order->users->first()->id);
        $this->assertEquals($item->name, $order->item->name);
        $this->assertEquals($item->id, $order->item->pid);
        $this->assertEquals($item->sid, $order->item->sid);
    }

    public function testUserCanNotStoreExistentItemWithoutId(): void
    {
        $this->assertTrue($this->isUserNotInOrder($order = $this->getOrder(), $user = $this->getUser()));
        $response = $this->actingAs($user)
            ->from($this->showUrl)
            ->post($this->existsUrl, [
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect($this->showUrl);
        $this->isUsersCountNotChanged($order);
        $response->assertSessionHasErrors('id');
    }

    public function testUserCanNotStoreExistentItemWithoutQty(): void
    {
        $this->assertTrue($this->isUserNotInOrder($order = $this->getOrder(), $user = $this->getUser()));
        $response = $this->actingAs($user)
            ->from($this->showUrl)
            ->post($this->existsUrl, [
                'id' => $order->id,
            ]);
        $response->assertRedirect($this->showUrl);
        $this->isUsersCountNotChanged($order);
        $response->assertSessionHasErrors('qty');
    }

    public function testUserCanNotStoreExistentItemWithWrongId(): void
    {
        $this->assertTrue($this->isUserNotInOrder($order = $this->getOrder(), $user = $this->getUser()));
        $response = $this->actingAs($user)
            ->from($this->showUrl)
            ->post($this->existsUrl, [
                'id' => 0,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->isUsersCountNotChanged($order);
        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
    }

    public function testUserCanNotStoreNewItemWithoutId(): void
    {
        $response = $this->actingAs($user = $this->getUser())
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'group' => $this->getGroup()->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect($this->showUrl);
        $this->assertCount($this->ordersCount, Order::all());
        $response->assertSessionHasErrors('id');
    }

    public function testUserCanNotStoreNewItemWithWrongId(): void
    {
        $response = $this->actingAs($user = $this->getUser())
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'id' => 0,
                'group' => $this->getGroup()->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->assertCount($this->ordersCount, Order::all());
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
    }

    public function testUserCanNotStoreNewItemWithoutGroup(): void
    {
        $response = $this->actingAs($user = $this->getUser())
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
        $response = $this->actingAs($user = $this->getUser())
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'id' => $this->getActualItem()->id,
                'group' => $this->getGroup()->id,
            ]);
        $response->assertRedirect($this->showUrl);
        $this->assertCount($this->ordersCount, Order::all());
        $response->assertSessionHasErrors('qty');
    }

    public function testUserCanNotStoreNewItemWithWrongGroup(): void
    {
        $response = $this->actingAs($user = $this->getUser())
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
        $group = $this->groupToArchive($this->getGroup());
        $response = $this->actingAs($user = $this->getUser())
            ->from($this->showUrl)
            ->post($this->newUrl, [
                'id' => $this->getActualItem()->id,
                'group' => $group->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->assertCount($this->ordersCount, Order::all());
        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
    }

    public function testUserCanNotStoreOrderTwice(): void
    {
        $response = $this->actingAs($user = ($order = $this->getOrder())->users->first())
            ->post($this->existsUrl, [
                'id' => $order->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->assertTrue($response->exception instanceof \App\Exceptions\Order\OrderNotCreatedException);
        $this->isUsersCountNotChanged($order);
        $this->assertEquals(
            $this->getOrderUserPivot($order)->qty, 
            $this->getOrderUserPivot(Order::findOrFail($order->id))->qty
        );
    }

}