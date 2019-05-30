<?php

namespace Tests\Feature;

use App\Order;
use App\Group;

class OrderTest extends BaseOrder
{
	
	protected $url = '/item/show';
    
    public function testUserCanNotSeeItemShowPageWithoutSearch(): void
    {
        $response = $this->get($this->url);
        $response->assertStatus(405);
    }

    public function testUserCanStoreExistentItem(): void
    {
    	$user = $this->createUser();
        $this->assertNotContains($user->id, $this->getItem()->order->users->pluck('id'));
        $response = $this->actingAs($user)
            ->from($this->url)
            ->post('/order/store_exists', [
                'id' => $this->getItem()->order->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/?page=');
        $this->assertCount(
            $this->getItem()->order->users->count() + 1, 
            $this->getItemById($this->getItem()->id)->order->users
        );
    }

    public function testUserCanStoreNewItem(): void
    {
        $this->getOrder()->delete();
        $item = $this->findItem();
        $response = $this->actingAs($this->getUser())
            ->followingRedirects()
            ->from($this->url)
            ->post('/order/store_new', [
                'id' => $item->id,
                'group' => $this->getGroup()->id,
                'qty' => $qty = $this->getQty()
            ]);
        $response->assertOk();
        $response->assertSee('Заказ успешно создан(а)');
        $this->assertEquals(1, $this->getOrdersCount());
        $this->assertCount(1, ($order = $this->getLastOrder())->users);
        $this->assertEquals($this->getUser()->id, $order->users->first()->id);
        $this->assertEquals($item->name, $order->item->name);
        $this->assertEquals($item->id, $order->item->pid);
        $this->assertEquals($item->sid, $order->item->sid);
    }

   public function testUserCanNotStoreExistentItemWithoutId(): void
   {
       $this->assertNotContains($this->getUser()->id, $this->getItem()->order->users->pluck('id'));
       $response = $this->actingAs($this->getUser())
           ->from($this->url)
           ->post('/order/store_exists', [
               'qty' => 7
           ]);
       $response->assertRedirect($url);
       $this->assertCount($item->order->users->count(), $this->findItem($item->id)->order->users);
       $response->assertSessionHasErrors('id');
   }
//
//    public function testUserCanNotStoreExistentItemWithoutQty(): void
//    {
//        $url = '/item/show';
//        $this->removeTestUserOrders();
//        $item = $this->getActualItem();
//        $this->assertNotContains($this->getTestUser()->first()->id, $item->order->users->pluck('id'));
//        $response = $this->authenticateByTest()
//            ->from($url)
//            ->post('/order/store_exists', [
//                'id' => $item->order->id,
//            ]);
//        $response->assertRedirect($url);
//        $this->assertCount($item->order->users->count(), $this->findItem($item->id)->order->users);
//        $response->assertSessionHasErrors('qty');
//    }
//
//    public function testUserCanNotStoreExistentItemWithWrongId(): void
//    {
//        $this->removeTestUserOrders();
//        $item = $this->getActualItem();
//        $this->assertNotContains($this->getTestUser()->first()->id, $item->order->users->pluck('id'));
//        $response = $this->authenticateByTest()
//            ->from('/item/show')
//            ->post('/order/store_exists', [
//                'id' => 0,
//                'qty' => 7
//            ]);
//        $response->assertRedirect('/');
//        $this->assertCount($item->order->users->count(), $this->findItem($item->id)->order->users);
//        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
//    }
//
//    public function testUserCanNotStoreNewItemWithoutId(): void
//    {
//        $url = '/item/show';
//        $this->removeTestUserOrders();
//        $user = $this->getTestUser()->first();
//        $items = $this->getLastItems();
//        $response = $this->authenticateByTest()
//            ->from($url)
//            ->post('/order/store_new', [
//                'group' => $this->getActualGroup()->id,
//                'qty' => 7
//            ]);
//        $response->assertRedirect($url);
//        $this->assertEquals($items, $this->getLastItems());
//        $response->assertSessionHasErrors('id');
//    }
//
//    public function testUserCanNotStoreNewItemWithWrongId(): void
//    {
//        $this->removeTestUserOrders();
//        $user = $this->getTestUser()->first();
//        $items = $this->getLastItems();
//        $response = $this->authenticateByTest()
//            ->from('/item/show')
//            ->post('/order/store_new', [
//                'id' => 0,
//                'group' => $this->getActualGroup()->id,
//                'qty' => 7
//            ]);
//        $response->assertRedirect('/');
//        $this->assertEquals($items, $this->getLastItems());
//        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
//    }
//
//    public function testUserCanNotStoreNewItemWithoutGroup(): void
//    {
//        $url = '/item/show';
//        $this->removeTestUserOrders();
//        $user = $this->getTestUser()->first();
//        $items = $this->getLastItems();
//        $response = $this->authenticateByTest()
//            ->from($url)
//            ->post('/order/store_new', [
//                'id' => $this->getArchivedItem()->pid,
//                'qty' => 7
//            ]);
//        $response->assertRedirect($url);
//        $this->assertEquals($items, $this->getLastItems());
//        $response->assertSessionHasErrors('group');
//    }
//
//    public function testUserCanNotStoreNewItemWithoutQty(): void
//    {
//        $url = '/item/show';
//        $this->removeTestUserOrders();
//        $user = $this->getTestUser()->first();
//        $items = $this->getLastItems();
//        $response = $this->authenticateByTest()
//            ->from($url)
//            ->post('/order/store_new', [
//                'id' => $this->getArchivedItem()->pid,
//                'group' => $this->getActualGroup()->id,
//            ]);
//        $response->assertRedirect($url);
//        $this->assertEquals($items, $this->getLastItems());
//        $response->assertSessionHasErrors('qty');
//    }
//
//    public function testUserCanNotStoreNewItemWithWrongGroup(): void
//    {
//        $this->removeTestUserOrders();
//        $user = $this->getTestUser()->first();
//        $items = $this->getLastItems();
//        $response = $this->authenticateByTest()
//            ->from('/item/show')
//            ->post('/order/store_new', [
//                'id' => $this->getArchivedItem()->pid,
//                'group' => 0,
//                'qty' => 7
//            ]);
//        $response->assertRedirect('/');
//        $this->assertEquals($items, $this->getLastItems());
//        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
//    }
//
//    public function testUserCanNotStoreNewItemInArchivedGroup(): void
//    {
//        $this->removeTestUserOrders();
//        $user = $this->getTestUser()->first();
//        $items = $this->getLastItems();
//        $response = $this->authenticateByTest()
//            ->from('/item/show')
//            ->post('/order/store_new', [
//                'id' => $this->getArchivedItem()->pid,
//                'group' => $this->getLastArchivedGroups()->first()->id,
//                'qty' => 7
//            ]);
//        $response->assertRedirect('/');
//        $this->assertEquals($items, $this->getLastItems());
//        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
//    }
//
//    public function testNotAuthtedUserCanNotSeeOrderDestroyBtn(): void
//    {
//        $response = $this->get('/');
//        $response->assertDontSee('Удалить');
//    }
//
//    public function testNotAuthtedUserCanNotDestroyOrder(): void
//    {
//        $usersOrder = $this->getUsersOrderPivot();
//        $response = $this->get('/order/destroy/'.$usersOrder->id);
//        $this->assertEquals($usersOrder, $this->getUsersOrderPivot());
//        $response->assertRedirect('/login');
//    }
//
//    public function testNotAuthorizedUserCanNotDestroyOrder(): void
//    {
//        $usersOrder = $this->getUsersOrderPivot();
//        $response = $this->actingAs($this->getOrdinaryRoleUser())->get('/order/destroy/'.$usersOrder->id);
//        $this->assertEquals($usersOrder, $this->getUsersOrderPivot());
//        $response->assertRedirect('/');
//    }

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