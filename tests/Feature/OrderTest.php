<?php

namespace Tests\Feature;

use Tests\Support\{Prepare, GroupTrait, ItemTrait, OrderTrait, UserTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;
use Illuminate\Support\Collection;

class OrderTest extends Prepare
{

    use UserTrait;
    use GroupTrait;
    use ItemTrait;
    use OrderTrait;
	
	protected $url = '/item/show';
    protected $storeExistsUrl = '/order/store_exists';
    protected $storeNewUrl = '/order/store_new';
    protected $destroyUrl = '/order/destroy/';
    protected $updateUrl = '/order/update';
    
    /*public function testUserCanNotSeeItemShowPageWithoutSearch(): void
    {
        $response = $this->get($this->url);
        $response->assertStatus(405);
    }

    public function testUserCanStoreExistentItem(): void
    {
        $this->userCanStoreExistentItem($this->url);
    }

    public function testUserCanStoreExistentItemFromIndexPage(): void
    {
        $this->userCanStoreExistentItem('/');
    }

    protected function userCanStoreExistentItem(string $url): void
    {
        $this->assertTrue($this->isUserNotInOrder());
        $response = $this->actingAs($this->getUser())
            ->from($url)
            ->followingRedirects()
            ->post($this->storeExistsUrl, [
                'id' => $this->getItem()->order->id,
                'qty' => $this->getQty()
            ]);
        $response->assertOk();
        $response->assertSee('Заказ успешно создан(а)');
        $this->assertCount($this->getOrderUsersCount() + 1, $this->getNewOrderUsers());
    }

    public function testUserCanStoreNewItem(): void
    {
        $item = $this->getActualItem();
        $response = $this->actingAs($this->getUser())
            ->followingRedirects()
            ->from($this->url)
            ->post($this->storeNewUrl, [
                'id' => $item->id,
                'group' => $this->getGroup()->id,
                'qty' => $qty = $this->getQty()
            ]);
        $response->assertOk();
        $response->assertSee('Заказ успешно создан(а)');
        $this->assertEquals($this->getOrdersCount() + 1, $this->getActualOrdersCount());
        $this->assertCount(1, ($order = $this->getLastOrder())->users);
        $this->assertEquals($this->getUser()->id, $order->users->first()->id);
        $this->assertEquals($item->name, $order->item->name);
        $this->assertEquals($item->id, $order->item->pid);
        $this->assertEquals($item->sid, $order->item->sid);
    }

    public function testUserCanNotStoreExistentItemWithoutId(): void
    {
        $this->assertTrue($this->isUserNotInOrder());
        $response = $this->actingAs($this->getUser())
            ->from($this->url)
            ->post($this->storeExistsUrl, [
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect($this->url);
        $this->isNoNewUsersIsOrder();
        $response->assertSessionHasErrors('id');
    }

    public function testUserCanNotStoreExistentItemWithoutQty(): void
    {
        $this->assertTrue($this->isUserNotInOrder());
        $response = $this->actingAs($this->getUser())
            ->from($this->url)
            ->post($this->storeExistsUrl, [
                'id' => $this->getItem()->order->id,
            ]);
        $response->assertRedirect($this->url);
        $this->isNoNewUsersIsOrder();
        $response->assertSessionHasErrors('qty');
    }

    public function testUserCanNotStoreExistentItemWithWrongId(): void
    {
        $this->assertTrue($this->isUserNotInOrder());
        $response = $this->actingAs($this->getUser())
            ->from($this->url)
            ->post($this->storeExistsUrl, [
                'id' => 0,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->isNoNewUsersIsOrder();
        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
    }

    public function testUserCanNotStoreNewItemWithoutId(): void
    {
        $response = $this->actingAs($this->getUser())
            ->from($this->url)
            ->post($this->storeNewUrl, [
                'group' => $this->getGroup()->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect($this->url);
        $this->isOrdersCountNotChanged();
        $response->assertSessionHasErrors('id');
    }

    public function testUserCanNotStoreNewItemWithWrongId(): void
    {
        $response = $this->actingAs($this->getUser())
            ->from($this->url)
            ->post($this->storeNewUrl, [
                'id' => 0,
                'group' => $this->getGroup()->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->isOrdersCountNotChanged();
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
    }

    public function testUserCanNotStoreNewItemWithoutGroup(): void
    {
        $response = $this->actingAs($this->getUser())
            ->from($this->url)
            ->post($this->storeNewUrl, [
                'id' => $this->getActualItem()->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect($this->url);
        $this->isOrdersCountNotChanged();
        $response->assertSessionHasErrors('group');
    }

    public function testUserCanNotStoreNewItemWithoutQty(): void
    {
        $response = $this->actingAs($this->getUser())
            ->from($this->url)
            ->post($this->storeNewUrl, [
                'id' => $this->getActualItem()->id,
                'group' => $this->getGroup()->id,
            ]);
        $response->assertRedirect($this->url);
        $this->isOrdersCountNotChanged();
        $response->assertSessionHasErrors('qty');
    }

    public function testUserCanNotStoreNewItemWithWrongGroup(): void
    {
        $response = $this->actingAs($this->getUser())
            ->from($this->url)
            ->post($this->storeNewUrl, [
                'id' => $this->getActualItem()->id,
                'group' => 0,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->isOrdersCountNotChanged();
        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
    }

    public function testUserCanNotStoreNewItemInArchivedGroup(): void
    {
        $this->groupToArchive();
        $response = $this->actingAs($this->getUser())
            ->from($this->url)
            ->post($this->storeNewUrl, [
                'id' => $this->getActualItem()->id,
                'group' => $this->getGroup()->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->isOrdersCountNotChanged();
        $this->assertTrue($response->exception instanceof \Illuminate\Database\Eloquent\ModelNotFoundException);
    }

    public function testNotAuthtedUserCanNotDestroyOrder(): void
    {
        $response = $this->get($this->destroyUrl . $this->getOrderUserPivot()->id);
        $this->isNoNewUsersIsOrder();
        $response->assertRedirect('/login');
    }

    public function testNotInOrderUserCanNotDestroyOrder(): void
    {
        $pivot = $this->getOrderUserPivot();
        $this->assertNotEquals($pivot->user_id, $this->getUser()->id);
        $response = $this->actingAs($this->getUser())
            ->get($this->destroyUrl.$pivot->id);
        $this->isNoNewUsersIsOrder();
        $response->assertRedirect('/');
    }

    public function testUserCanDestroySelfSingleUserOrder(): void
    {
        $order = $this->getSingleUserOrder();
        $id = $order->id;
        $pivot = $order->users->first()->pivot;
        $response = $this->actingAs($this->getUserById($pivot->user_id))
            ->get($this->destroyUrl . $pivot->id);
        $this->assertFalse($this->getAllOrders()->pluck('id')->contains($id));
        $this->assertEquals($this->getItemsCount() - 1, $this->getActualItemsCount());
        $response->assertRedirect('/');
    }

    public function testUserCanDestroySelfMultipleUserOrder(): void
    {
        $order = $this->getMultipleUsersOrder();
        $pivot = $order->users->first()->pivot;
        $response = $this->actingAs($this->getUserById($pivot->user_id))
            ->get($this->destroyUrl . $pivot->id);
        $this->assertCount($order->users->count() -1, $this->getMultipleUsersOrder()->users);
        $this->assertEquals($this->getOrdersCount(), $this->getActualOrdersCount());
        $this->assertEquals($this->getItemsCount(), $this->getActualItemsCount());
        $response->assertRedirect('/');
    }

    public function testAdminUserCanDestroySingleUserOrder(): void
    {
        $order = $this->getSingleUserOrder();
        $id = $order->id;
        $pivot = $order->users->first()->pivot;
        $response = $this->actingAs($this->getAdmin())
            ->get($this->destroyUrl . $pivot->id);
        $this->assertFalse($this->getAllOrders()->pluck('id')->contains($id));
        $this->assertEquals($this->getItemsCount() - 1, $this->getActualItemsCount());
        $response->assertRedirect('/');
    }

    public function testAdminUserCanDestroyMultipleUserOrder(): void
    {
        $order = $this->getMultipleUsersOrder();
        $pivot = $order->users->first()->pivot;
        $response = $this->actingAs($this->getAdmin())
            ->get($this->destroyUrl . $pivot->id);
        $this->assertCount($order->users->count() -1, $this->getMultipleUsersOrder()->users);
        $this->assertEquals($this->getOrdersCount(), $this->getActualOrdersCount());
        $this->assertEquals($this->getItemsCount(), $this->getActualItemsCount());
        $response->assertRedirect('/');
    }

    public function testAdminUserCanNotDestroyArchivedOrder(): void
    {
        $this->groupToArchive();
        $order = $this->getGroup()->first()->orders->first();
        $pivot = $order->users->first()->pivot;
        $response = $this->actingAs($this->getAdmin())
            ->get($this->destroyUrl.$pivot->id);
        $this->assertEquals($this->getOrdersCount(), $this->getActualOrdersCount());
        $this->assertEquals($this->getItemsCount(), $this->getActualItemsCount());
        $this->assertCount($order->users->count(), $this->getOrderById($order->id)->users);
        $response->assertRedirect('/');
        $this->assertTrue($response->exception instanceof \App\Exceptions\NotFoundException);
    }

    public function testUserCanNotStoreOrderTwice(): void
    {
        $response = $this->actingAs($user = $this->getOrder()->users->first())
            ->post($this->storeExistsUrl, [
                'id' => $this->getOrder()->id,
                'qty' => $this->getQty()
            ]);
        $response->assertRedirect('/');
        $this->assertTrue($response->exception instanceof \App\Exceptions\Order\OrderNotCreatedException);
        $this->isNoNewUsersIsOrder();
        $this->assertEquals($this->getOrderUserPivot()->qty, $this->getNewOrderUserPivot()->qty);
    }

    public function testUserCanUpdateSelfOrder(): void
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
    }*/

    public function testAdminCanUpdateAnyOrder(): void
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
        
    }

    /*public function testUserCanNotUpdateAnotherUsersOrder(): void
    {

    }

    public function testUserCanNotUpdateSelfOrderWithoutId(): void
    {

    }

    public function testUserCanNotUpdateSelfOrderWithoutQty(): void
    {

    }*/

    protected function isOrdersCountNotChanged(): void
    {
        $this->assertEquals($this->getOrdersCount(), $this->getActualOrdersCount());
    }

    protected function isNoNewUsersIsOrder(): void
    {
        $this->assertCount($this->getOrderUsersCount(), $this->getNewOrderUsers());
    }

    protected function isUserNotInOrder(\App\User $user = null): bool
    {
        return !$this->getItem()->order->users->pluck('id')->contains(($user ?? $this->getUser())->id);
    }

    protected function getOrderUsersCount(): int
    {
        return $this->getItem()->order->users->count();
    }

    protected function getNewOrderUsers(): Collection
    {
        return $this->getItemById($this->getItem()->id)->order->users;
    }

    protected function getOrderUserPivot(): object
    {
        return $this->getOrder()->users->first()->pivot;
    }

    protected function getNewOrderUserPivot(): object
    {
        return $this->getOrderById($this->getOrder()->id)->users->first()->pivot;
    }

}