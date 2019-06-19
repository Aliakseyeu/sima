<?php

namespace Tests\Feature;

use App\{Group, Item, Order, User};
use Tests\Support\{Prepare, GroupTrait, ItemTrait, OrderTrait, UserTrait};
use \Illuminate\Foundation\Testing\TestResponse as Response;
use Illuminate\Support\Collection;

class OrderUpdateTest extends Prepare
{

    use OrderTrait;
    use UserTrait;

    protected $url = '/order/update';

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testUserCanUpdateSelfOrder(): void
    {
        $response = $this->actingAs($user = ($order = $this->getMultipleUsersOrder())->users->first())
            ->followingRedirects()
            ->post($this->url, [
                'id' => ($pivot = $this->getOrderUserPivot($order))->id,
                'qty' => $pivot->qty + 1
            ]);
        $response->assertOk();
        $response->assertSee('Заказ успешно обновлен(а)');
        $this->assertEquals($pivot->qty + 1, $this->getOrderUserPivot($order)->qty);
    }

    public function testAdminCanUpdateAnyOrder(): void
    {
        $this->isUserNotInOrder($order = $this->getMultipleUsersOrder(), $user = $this->getAdmin());
        $response = $this->actingAs($user)
            ->followingRedirects()
            ->post($this->url, [
                'id' => ($pivot = $this->getOrderUserPivot($order))->id,
                'qty' => $pivot->qty + 1
            ]);
        $response->assertOk();
        $response->assertSee('Заказ успешно обновлен(а)');
        $this->assertEquals($pivot->qty + 1, $this->getOrderUserPivot($order)->qty);
    }

    public function testUserCanNotUpdateAnotherUsersOrder(): void
    {
        $this->isUserNotInOrder($order = $this->getMultipleUsersOrder(), $user = $this->getUser());
        $response = $this->actingAs($user)
            ->post($this->url, [
                'id' => ($pivot = $this->getOrderUserPivot($order))->id,
                'qty' => $pivot->qty + 1
            ]);
        $response->assertRedirect('/');
        $this->assertEquals($pivot->qty, $this->getOrderUserPivot($order)->qty);
        $this->assertTrue($response->exception instanceof \App\Exceptions\User\NotAuthorizedException);
    }

    public function testUserCanNotUpdateSelfOrderWithoutId(): void
    {
        $response = $this->actingAs($user = ($order = $this->getMultipleUsersOrder())->users->first())
            ->post($this->url, [
                'qty' => ($pivot = $this->getOrderUserPivot($order))->qty + 1
            ]);
        $response->assertRedirect('/');
        $this->assertEquals($pivot->qty, $this->getOrderUserPivot($order)->qty);
        $response->assertSessionHasErrors('id');
    }

    public function testUserCanNotUpdateSelfOrderWithoutQty(): void
    {
        $response = $this->actingAs($user = ($order = $this->getMultipleUsersOrder())->users->first())
            ->post($this->url, [
                'id' => ($pivot = $this->getOrderUserPivot($order))->id,
            ]);
        $response->assertRedirect('/');
        $this->assertEquals($pivot->qty, $this->getOrderUserPivot($order)->qty);
        $response->assertSessionHasErrors('qty');
    }

}