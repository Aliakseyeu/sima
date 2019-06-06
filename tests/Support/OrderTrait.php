<?php

namespace Tests\Support;

use App\{Order, User};
use Illuminate\Support\Collection;

trait OrderTrait
{

    protected $showUrl = '/item/show';

    // protected function isOrdersCountNotChanged(): void
    // {
    //     $this->assertEquals($this->getOrdersCount(), $this->getActualOrdersCount());
    // }

    protected function isUsersCountNotChanged(Order $order): void
    {
        $this->assertCount($order->users->count(), Order::findOrFail($order->id)->users);
    }

    protected function isUserNotInOrder(Order $order, User $user): bool
    {
        return !$order->users->pluck('id')->contains($user->id);
    }

    // protected function getOrderUsersCount(): int
    // {
    //     return $this->getItem()->order->users->count();
    // }

    // protected function getNewOrderUsers(): Collection
    // {
    //     return $this->getItemById($this->getItem()->id)->order->users;
    // }

    protected function getOrderUserPivot(Order $order): object
    {
        return $order->users->first()->pivot;
    }

    // protected function getNewOrderUserPivot(): object
    // {
    //     return $this->getOrderById($this->getOrder()->id)->users->first()->pivot;
    // }
    // 
    public function getQty(): int
    {
        return rand(1, 10);
    }



    // private $order;
    // private $ordersCount;

    // public function orderTrait(): void
    // {
    //     $this->order = Order::first();
    //     $this->ordersCount = $this->getActualOrdersCount();
    // }

    // public function getActualOrdersCount(): int
    // {
    //     return Order::count();
    // }

    // public function getSingleUserOrder(): Order
    // {
    //     return Order::findOrFail(1);
    // }

    // public function getMultipleUsersOrder(): Order
    // {
    //     return Order::findOrFail(2);
    // }

    // public function getOrderById(int $id): Order
    // {
    //     return Order::findOrFail($id);
    // }

    // public function getAllOrders(): Collection
    // {
    //     return Order::all();
    // }

    

    // public function getLastOrder(): Order
    // {
    //     return Order::orderBy('id', 'desc')->first();
    // }

    // public function getOrder(): Order
    // {
    //     return $this->order;
    // }

    // public function getOrdersCount(): int
    // {
    //     return $this->ordersCount;
    // }
    
}