<?php

namespace Tests\Feature;

use DB;
use App\{Group, Item, Status, Order};
use App\Repositories\ItemRepository;
use Illuminate\Database\Eloquent\Collection;

class BaseOrderTest extends BaseTest
{

    public function testTrue(): void
    {
        $this->assertTrue(true);
    }

    protected function findItem(int $id): Item
    {
        return Item::find($id);
    }
     
    protected function getActualItem(): Item
    {
        $items = $this->getLastItems();
        $repository = new ItemRepository(false);
        foreach($items as $item){
            $search = $repository->where('sid', $item->sid);
            if($search->id){
                return $item;
            }
        }
    }

    protected function getArchivedItem(): Item
    {
        $repository = new ItemRepository(false);
        foreach($this->getLastArchivedGroups() as $group){
            foreach($group->orders as $order){
                if($this->isAllSimilarItemsAreArchived($order)){
                    return $order->item;
                }
            }
        }
    }

    protected function isAllSimilarItemsAreArchived(Order $order): bool
    {
        foreach(Item::whereSid($order->item->sid)->get() as $item){
            if($item->order->group->status->slug != 'archived'){
                return false;
            }
        }
        return true;
    }

    protected function getLastArchivedGroups(): Collection
    {
        return (new Status)->archived()->groups->sortByDesc('id');
    }

    protected function getLastItems(): Collection
    {
        return Item::orderBy('id', 'DESC')->take(100)->get();
    }

    protected function getLastOrders(): Collection
    {
        return Order::orderBy('id', 'DESC')->take(100)->get();
    }

    protected function getActualGroup(): Group
    {
        return (new Status)->new()->groups->first();
    }

    protected function getActualOrder(): Order
    {
        return $this->getActualGroup()->orders->first();
    }

    protected function getSingleUserOrder(): Order
    {
        foreach($this->getLastOrders() as $order){
            if($order->users->count() ==1){
                return $order;
            }
        }
    }

    protected function getMultipleUsersOrder(): object
    {
        foreach($this->getLastOrders() as $order){
            if($order->users->count() > 1){
                return $order;
            }
        }
    }

    protected function getUsersOrderPivot(): object
    {
        return $this->getActualOrder()->users->first()->pivot;
    }

    protected function getItemCreateUrl(Group $group): string
    {
        return '/item/create/'.$group->id;
    }

    protected function removeTestUserOrders(): void
    {
        $user = $this->getTestUser()->first();
        $ordersIds = DB::table('order_user')->where('user_id', $user->id)->get()->map(function($item){
            return $item->order_id;
        });
        $orders = Order::whereIn('id', $ordersIds)->get();
        foreach($orders as $order){
            if($order->users->count() == 1){
                DB::transaction(function() use ($order){
                    $order->users->first()->pivot->delete();
                    $order->item->delete();
                    $order->delete();
                });
            } else {
                $order->users->where('id', $user->id)->first()->pivot->delete();
            }
        }
    }

    protected function restoreSingleUserOrder(Order $order, Item $item, object $pivot): void
    {
        DB::transaction(function() use ($order, $item, $pivot){
            $order->save();
            $order->item->save($item->toArray());
            $order->users()->attach($pivot->user_id, $pivot->toArray());
        });
    }

    protected function restoreMultipleUsersOrder(Order $order, object $pivot): void
    {
        $order->users()->attach($pivot->user_id, $pivot->toArray());
    }

    protected function restoreUpdatedOrder(object $pivot): void
    {
        DB::table('order_user')->where('id', $pivot->id)->update(['qty' => $pivot->qty - 1]);
    }

}