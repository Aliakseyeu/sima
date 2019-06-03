<?php

namespace Tests\Support;

use App\Order;

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

    public function getQty(): int
    {
        return rand(1, 10);
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