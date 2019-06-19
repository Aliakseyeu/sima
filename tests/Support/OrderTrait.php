<?php

namespace Tests\Support;

use App\{Item, Order, User};
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

trait OrderTrait
{

    protected $showUrl = '/item/show';

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

    protected function getOrderUserPivot(Order $order): object
    {
        return Order::findOrFail($order->id)->users->first()->pivot;
    }

    protected function getOrder(): Order
    {
        return Order::first();
    }

    public function getQty(): int
    {
        return rand(1, 10);
    }

}