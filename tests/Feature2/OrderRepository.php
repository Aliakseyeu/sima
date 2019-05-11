<?php

namespace Tests\Feature;

use App\Order;

class OrderRepository
{
    
    protected $order;

    public function find(int $id): void
    {
        $this->order = Order::findOrFail($id);
    }

    public function restore(): void
    {
        
    }

    public function get(): Order
    {
        return $this->order;
    }

}
