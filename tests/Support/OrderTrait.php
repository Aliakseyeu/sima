<?php

namespace Tests\Support;

use App\{Item, Order, User};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait OrderTrait
{

    protected $showUrl = '/item/show';

    // protected function isOrdersCountNotChanged(): void
    // {
    //     $this->assertEquals($this->getOrdersCount(), $this->getActualOrdersCount());
    // }

    protected function isUsersCountNotChanged(Order $order, int $sub = 0): void
    {
        $this->assertCount($order->users->count() - $sub, Order::findOrFail($order->id)->users);
    }

    protected function isOrderNotDeleted(Order $order): bool
    {
        return $this->isModelContainsId(new Order, $order->id) && $this->isModelContainsId(new Item, $order->item->id);
    }

    protected function isOrderDeleted(Order $order): bool
    {
        return !$this->isModelContainsId(new Order, $order->id) && !$this->isModelContainsId(new Item, $order->item->id);
    }

    protected function isModelContainsId(Model $model, int $id): bool
    {
        return $model->all()->pluck('id')->contains($id);
    }

    protected function isUserNotInOrder(Order $order, User $user): bool
    {
        return !$order->users->pluck('id')->contains($user->id);
    }

    protected function getSingleUserOrder(): Order
    {
        return $this->findOrder(1);
    }

    protected function getMultipleUsersOrder(): Order
    {
        return $this->findOrder(3);
    }

    protected function findOrder(int $id): Order
    {
        return Order::with('item')->findOrFail($id);
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
        return Order::findOrFail($order->id)->users->first()->pivot;
    }

    protected function getOrder(): Order
    {
        return Order::first();
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