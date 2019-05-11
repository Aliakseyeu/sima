<?php

namespace App\Objects;

use App\{Group, User};
use App\Services\ItemRepository;

class ReportItem
{
    
    protected $user;
    protected $group;
    protected $orders;
    protected $itemService;

    public function __construct(User $user, Group $group){
        $this->orders = collect();
        $this->user = $user;
        $this->group = $group;
        $this->itemService = new ItemRepository;
        $this->findOrders();
    }

    protected function findOrders(){
        $orders = $this->user->orders()->where(['group_id' => $this->group->id])->get();
        $orders = $this->itemService->addItemsInfo($orders);
        // $orders->map(function($order){
        //     $order->user($this->user)->pivot;
        // });
        $orders = $this->itemService->addDeliveryPrice($orders);
        // dd($orders->first()->users($this->user)->get()->first()->pivot);
        $this->orders = $orders;
    }

    public function getOrders(){
        return $this->orders;
    }

    public function getUser(){
        return $this->user;
    }
    
}
