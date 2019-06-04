<?php

namespace Tests\Support;

use App\Order;
use Illuminate\Support\Collection;

trait OrderTrait
{

    private $order;
    private $ordersCount;

    public function orderTrait(): void
    {
        $this->order = Order::first();
        $this->ordersCount = $this->getActualOrdersCount();
    }

    public function getActualOrdersCount(): int
    {
        return Order::count();
    }

    public function getSingleUserOrder(): Order
    {
        return Order::findOrFail(1);
    }

    public function getMultipleUsersOrder(): Order
    {
        return Order::findOrFail(2);
    }

    public function getOrderById(int $id): Order
    {
        return Order::findOrFail($id);
    }

    public function getAllOrders(): Collection
    {
        return Order::all();
    }

    public function getQty(): int
    {
        return rand(1, 10);
    }

    public function getLastOrder(): Order
    {
        return Order::orderBy('id', 'desc')->first();
    }

    public function getOrder(): Order
    {
        return $this->order;
    }

    public function getOrdersCount(): int
    {
        return $this->ordersCount;
    }
    
}