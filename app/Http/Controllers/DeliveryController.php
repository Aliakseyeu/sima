<?php

namespace App\Http\Controllers;

use Auth;
use App\{Order, Repositories\DeliveryRepository};

class DeliveryController extends Controller
{

    protected $order;
    protected $deliveryRepository;

    public function __construct(Order $order, DeliveryRepository $deliveryRepository)
    {
        $this->order = $order;
        $this->deliveryRepository = $deliveryRepository;
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \App\Exceptions\NotFoundException
     */
    public function update(int $id)
    {
        $pivot = $this->order->findOrderUserBuilderOrException($id);
        $order = $this->order->findOrFail($pivot->first()->order_id);
        $delivery = $this->deliveryRepository->getDeliveryPrice($order->item->pid, $pivot->first()->qty);
        if($order->updatePivotByBuilder($pivot, $this->order->getOrderUserData($pivot->first()->qty, $delivery))){
            return back()->withSuccess([__('messages.updated', ['name'=>__('messages.delivery')])]);
        }
        return back()->withErrors([__('messages.error.updated')]);
    }

}
