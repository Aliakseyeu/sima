<?php

namespace App\Http\Controllers;

use App\{Item, Notification, Order, Repositories\DeliveryRepository, Repositories\ItemRepository, Status};

class IndexController extends Controller
{
    
    protected $notification;

    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function index(Status $status)
    {
        $groups = $status->new()->groups()->paginate(1);
        $news = $this->notification->actual();
        return view('orders.list.new', compact('groups', 'news'));
    }

}
