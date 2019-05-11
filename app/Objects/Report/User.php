<?php
/**
 * Created by PhpStorm.
 * User: Andrei
 * Date: 28.10.18
 * Time: 13:32
 */

namespace App\Objects\Report;

use App\{Order, User as UserModel};

class User
{
    
    protected $orders;
    protected $user;

    public function __construct(UserModel $user)
    {
        $this->user = $user;
        $this->orders = collect();
    }

    public function addOrder(Order $order)
    {
        $this->orders->push($order);
    }

    public function getOrders(): \Illuminate\Support\Collection
    {
        return $this->orders;
    }

    public function __get(string $var)
    {
        return $this->user->$var;
    }

}