<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use Route;
use App\{Status, Order};
use App\Http\Requests\{OrderStoreExistsRequest, OrderStoreNewRequest, OrderUpdateRequest};
use App\Repositories\{DeliveryRepository, ItemRepository};
use App\Services\{OrderService};

class OrderController extends Controller
{

    protected $order;
    protected $status;
    protected $deliveryRepository;
    protected $itemRepository;
    protected $orderService;

    public function __construct(DeliveryRepository $deliveryRepository,
                                ItemRepository $itemRepository,
                                OrderService $orderService,
                                Order $order,
                                Status $status)
    {
        $this->deliveryRepository = $deliveryRepository;
        $this->itemRepository = $itemRepository;
        $this->orderService = $orderService;
        $this->order = $order;
        $this->status = $status;
    }

    public function create(int $group)
    {
        return view('order.create', compact('group'));
    }

    public function storeExists(OrderStoreExistsRequest $request)
    {
        $order = $this->order->findOrFail($request->id);
        $delivery = $this->deliveryRepository->getDeliveryPrice($order->item->pid, $request->qty);
        if($this->orderService->attachUser($order, Auth::id(), $request->qty, $delivery)){
            return redirect($this->getRedirectUrl($request))->withSuccess([__('messages.stored', ['name'=>__('messages.order')])]);
        };
        return redirect($this->getRedirectUrl($request))->withErrors(__('messages.error.stored'));
    }

    public function storeNew(OrderService $orderService, OrderStoreNewRequest $request)
    {
        $item = $this->itemRepository->find($request->id);
        $delivery = $this->deliveryRepository->getDeliveryPrice($item->id, $request->qty);
        $group = $this->status->new()->groups()->findOrFail($request->group);
        if($orderService->storeNew(Auth::id(), $group, $item, $delivery, $request->qty)){
            return redirect($this->getRedirectUrl($request))->withSuccess([__('messages.stored', ['name'=>__('messages.order')])]);
        }
        return redirect($this->getRedirectUrl($request))->withErrors(__('messages.error.stored'));
    }

    public function edit(int $id)
    {
        return view('orders.edit.edit', compact('id'));
    }

    public function update(OrderUpdateRequest $request)
    {
        $pivot = $this->order->findOrderUserBuilderOrException($request->id);
        if(!Auth::user()->isAdmin() && Auth::id() != $pivot->first()->user_id){
            throw new \App\Exceptions\NotAuthorizedException;
        }
        $order = $this->order->findOrFail($pivot->first()->order_id);
        $delivery = $this->deliveryRepository->getDeliveryPrice($order->item->pid, $request->qty);
        if($pivot->update($this->order->getOrderUserData($request->qty, $delivery))){
            return back()->withSuccess([__('messages.updated', ['name'=>__('messages.order')])]);
        }
        return back()->withErrors([__('messages.error.updated')]);
    }

    public function destroy(int $id)
    {
        $pivot = $this->order->findOrderUserBuilderOrException($id);
        $order = $this->order->findOrFail($pivot->first()->order_id);
        if($this->orderService->destroyOrder($pivot, $order)){
            return back()->withSuccess([__('messages.destroyed', ['name'=>__('messages.order')])]);
        }
        return back()->withErrors([__('messages.error.destroyed')]);
    }

    protected function getRedirectUrl(Request $request): string {
        return '/'. !empty($request->page) ? '?page='.$request->page : '';
    }
}
