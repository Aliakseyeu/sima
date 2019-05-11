<?php

namespace Tests\Feature;

use Tests\TestCase;

use App\Item;

class OrderTest extends TestCase
{

    use Traits\GroupTrait;
    use Traits\ItemTrait;
    use Traits\OrderTrait;
    use Traits\UserTrait;

    protected $from = '/item/show';
    
    public function testUserCanNotSeeItemShowPageWithoutSearch(): void
    {
        $response = $this->get('/item/show');
        $response->assertStatus(405);
    }

    public function testUserCanStoreExistentItem(): void
    {
        $this->removeTestUserOrders();
        $item = $this->getActualItem();
        $this->isUserNotInOrder($item);
        $response = $this->authenticateByTest()
            ->from($this->from)
            ->post('/order/store_exists', [
                'id' => $item->order->id,
                'qty' => 7
            ]);
        $response->assertRedirect('/?page=');
        $this->assertCount($item->order->users->count() + 1, $this->findItem($item->id)->order->users);
        $this->removeTestUserOrders();
    }

    public function testUserCanStoreNewItem(): void
    {
        $this->removeTestUserOrders();
        $item = $this->getArchivedItem();
        $user = $this->getTestUser()->first();
        $response = $this->authenticateByTest()
            ->from($this->from)
            ->post('/order/store_new', [
                'id' => $item->pid,
                'group' => $this->getActualGroup()->id,
                'qty' => 7
            ]);
        $response->assertRedirect('/?page=');
        $addedItem = $this->getLastItems()->first();
        $this->assertCount(1, $addedItem->order->users);
        $this->assertEquals($user->id, $addedItem->order->users->first()->id);
        $this->assertEquals($item->name, $addedItem->name);
        $this->assertEquals($item->pid, $addedItem->pid);
        $this->assertEquals($item->sid, $addedItem->sid);
        $this->removeTestUserOrders();
    }

    public function testUserCanNotStoreExistentItemWithoutId(): void
    {
        $this->removeTestUserOrders();
        $item = $this->getActualItem();
        $this->isUserNotInOrder($item);
        $response = $this->authenticateByTest()
            ->from($this->from)
            ->post('/order/store_exists', [
                'qty' => 7
            ]);
        $response->assertRedirect($this->from);
        $this->assertCount($item->order->users->count(), $this->findItem($item->id)->order->users);
        $response->assertSessionHasErrors('id');
    }

    protected function isUserNotInOrder(Item $item): void
    {        
        $this->assertNotContains($this->getTestUser()->first()->id, $item->order->users->pluck('id'));
    }

    /*public function testUserCanNotStoreExistentItemWithoutQty(): void
    {
        $url = '/item/show';
        $this->removeTestUserOrders();
        $item = $this->getActualItem();
        $this->assertNotContains($this->getTestUser()->first()->id, $item->order->users->pluck('id'));
        $response = $this->authenticateByTest()
            ->from($url)
            ->post('/order/store_exists', [
                'id' => $item->order->id,
            ]);
        $response->assertRedirect($url);
        $this->assertCount($item->order->users->count(), $this->findItem($item->id)->order->users);
        $response->assertSessionHasErrors('qty');
    }

    public function testUserCanNotStoreExistentItemWithWrongId(): void
    {
        $this->removeTestUserOrders();
        $item = $this->getActualItem();
        $this->assertNotContains($this->getTestUser()->first()->id, $item->order->users->pluck('id'));
        $response = $this->authenticateByTest()
            ->from('/item/show')
            ->post('/order/store_exists', [
                'id' => 0,
                'qty' => 7
            ]);
        $response->assertRedirect('/');
        $this->assertCount($item->order->users->count(), $this->findItem($item->id)->order->users);
        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
    }

    public function testUserCanNotStoreNewItemWithoutId(): void
    {
        $url = '/item/show';
        $this->removeTestUserOrders();
        $user = $this->getTestUser()->first();
        $items = $this->getLastItems();
        $response = $this->authenticateByTest()
            ->from($url)
            ->post('/order/store_new', [
                'group' => $this->getActualGroup()->id,
                'qty' => 7
            ]);
        $response->assertRedirect($url);
        $this->assertEquals($items, $this->getLastItems());
        $response->assertSessionHasErrors('id');
    }

    public function testUserCanNotStoreNewItemWithWrongId(): void
    {
        $this->removeTestUserOrders();
        $user = $this->getTestUser()->first();
        $items = $this->getLastItems();
        $response = $this->authenticateByTest()
            ->from('/item/show')
            ->post('/order/store_new', [
                'id' => 0,
                'group' => $this->getActualGroup()->id,
                'qty' => 7
            ]);
        $response->assertRedirect('/');
        $this->assertEquals($items, $this->getLastItems());
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
    }

    public function testUserCanNotStoreNewItemWithoutGroup(): void
    {
        $url = '/item/show';
        $this->removeTestUserOrders();
        $user = $this->getTestUser()->first();
        $items = $this->getLastItems();
        $response = $this->authenticateByTest()
            ->from($url)
            ->post('/order/store_new', [
                'id' => $this->getArchivedItem()->pid,
                'qty' => 7
            ]);
        $response->assertRedirect($url);
        $this->assertEquals($items, $this->getLastItems());
        $response->assertSessionHasErrors('group');
    }

    public function testUserCanNotStoreNewItemWithoutQty(): void
    {
        $url = '/item/show';
        $this->removeTestUserOrders();
        $user = $this->getTestUser()->first();
        $items = $this->getLastItems();
        $response = $this->authenticateByTest()
            ->from($url)
            ->post('/order/store_new', [
                'id' => $this->getArchivedItem()->pid,
                'group' => $this->getActualGroup()->id,
            ]);
        $response->assertRedirect($url);
        $this->assertEquals($items, $this->getLastItems());
        $response->assertSessionHasErrors('qty');
    }

    public function testUserCanNotStoreNewItemWithWrongGroup(): void
    {
        $this->removeTestUserOrders();
        $user = $this->getTestUser()->first();
        $items = $this->getLastItems();
        $response = $this->authenticateByTest()
            ->from('/item/show')
            ->post('/order/store_new', [
                'id' => $this->getArchivedItem()->pid,
                'group' => 0,
                'qty' => 7
            ]);
        $response->assertRedirect('/');
        $this->assertEquals($items, $this->getLastItems());
        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
    }

    public function testUserCanNotStoreNewItemInArchivedGroup(): void
    {
        $this->removeTestUserOrders();
        $user = $this->getTestUser()->first();
        $items = $this->getLastItems();
        $response = $this->authenticateByTest()
            ->from('/item/show')
            ->post('/order/store_new', [
                'id' => $this->getArchivedItem()->pid,
                'group' => $this->getLastArchivedGroups()->first()->id,
                'qty' => 7
            ]);
        $response->assertRedirect('/');
        $this->assertEquals($items, $this->getLastItems());
        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
    }

    public function testNotAuthtedUserCanNotSeeOrderDestroyBtn(): void
    {
        $response = $this->get('/');
        $response->assertDontSee('Удалить');
    }

    public function testNotAuthtedUserCanNotDestroyOrder(): void
    {
        $usersOrder = $this->getUsersOrderPivot();
        $response = $this->get('/order/destroy/'.$usersOrder->id);
        $this->assertEquals($usersOrder, $this->getUsersOrderPivot());
        $response->assertRedirect('/login');
    }

    public function testNotAuthorizedUserCanNotDestroyOrder(): void
    {
        $usersOrder = $this->getUsersOrderPivot();
        $response = $this->actingAs($this->getOrdinaryRoleUser())->get('/order/destroy/'.$usersOrder->id);
        $this->assertEquals($usersOrder, $this->getUsersOrderPivot());
        $response->assertRedirect('/');
    }

    /*public function testAuthorizedUserCanDestroySingleUserOrder(): void
    {
        $existentOrder = $this->getSingleUserOrder();
        $existentItem = $existentOrder->item;
        $existentPivot = $existentOrder->users->first()->pivot;
        $response = $this->actingAs($this->getAdminRoleUser())->get('/order/destroy/'.$existentPivot->id);
        $removedOrder = $this->getSingleUserOrder();
        $this->assertNotEquals($existentOrder, $removedOrder);
        $this->assertNotEquals($existentItem, $removedOrder->item);
        $this->assertNotEquals($existentPivot, $removedOrder->users->first()->pivot);
        $response->assertRedirect('/');
        $this->restoreSingleUserOrder($existentOrder, $existentItem, $existentPivot);
    }

    public function testAdminUserCanDestroyMultipleUsersOrder(): void
    {
        $existentOrder = $this->getMultipleUsersOrder();
        $existentPivot = $existentOrder->users->first()->pivot;
        $response = $this->actingAs($this->getAdminRoleUser())->get('/order/destroy/'.$existentPivot->id);
        $this->assertNotEquals($existentPivot, $this->getMultipleUsersOrder()->users->first()->pivot);
        $response->assertRedirect('/');
        $this->restoreMultipleUsersOrder($existentOrder, $existentPivot);
    }

    public function testUserCanDestroySelfOrder(): void
    {
        $existentOrder = $this->getMultipleUsersOrder();
        $existentPivot = $existentOrder->users->first()->pivot;
        $response = $this->actingAs($existentOrder->users->first())->get('/order/destroy/'.$existentPivot->id);
        $this->assertNotEquals($existentPivot, $this->getMultipleUsersOrder()->users->first()->pivot);
        $response->assertRedirect('/');
        $this->restoreMultipleUsersOrder($existentOrder, $existentPivot);
    }

    public function testUserCanNotDestroyArchivedOrder(): void
    {
        $archivedPivot = $this->getArchivedItem()->order->users->first()->pivot;
        $response = $this->actingAs($this->getAdminRoleUser())->get('/order/destroy/'.$archivedPivot->id);
        $this->assertEquals($archivedPivot, $this->getArchivedItem()->order->users->first()->pivot);
        $response->assertRedirect('/');
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
    }*/

    

}