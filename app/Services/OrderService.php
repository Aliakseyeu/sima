<?php

namespace App\Services;

use Illuminate\Database\Query\Builder;
use Ixudra\Curl\Facades\Curl;
use App\{Group,
    Item,
    Order,
    OrderUser,
    Status,
    User};
use App\Objects\{ Delivery, Item as ItemObject, Report, ReportItem, Resp};
use App\Exceptions\{BaseException, NotFoundException, Order\OrderNotCreatedException, User\NotAuthorizedException};
use Illuminate\Support\{Carbon, Collection};
use Auth;
use DB;
use Request;

class OrderService extends Service
{

    protected $item;
    protected $order;

    public function __construct(Item $item, Order $order)
    {
        $this->item = $item;
        $this->order = $order;
    }

    public function attachUser(Order $order, int $user, int $qty, Delivery $delivery): bool
    {
        if($order->findUser($user)){
            throw new OrderNotCreatedException(__('messages.order.user.exists'));
        }
        return $order->attachUser($user, $order->getOrderUserData($qty, $delivery));
    }

    public function storeNew(int $user, Group $group, ItemObject $itemObject, Delivery $delivery, int $qty): bool
    {
        return DB::transaction(function() use ($itemObject, $group, $user, $delivery, $qty){
            $group->orders()->save($this->order);
            $this->order->item()->save($this->item->fill($this->item->getFillData($itemObject)));
            return $this->order->attachUser($user, $this->order->getOrderUserData($qty, $delivery));
        });
    }

    public function destroyOrder(Builder $pivot, Order $order): bool
    {
        if($order->group->status->isArchived()){
            throw new NotFoundException('group');
        }
        if(!$this->isAuthorized($pivot->first()->user_id)){
            return false;
        }
        return DB::transaction(function() use ($pivot, $order){
            $pivot->delete();
            if($order->users->count() > 0){
                return true;
            }
            $order->item->delete();
            return $order->delete();
        });
    }

    public function updateOrder(Builder $pivot, array $data): bool
    {
        if($order->group->status->isArchived()){
            throw new NotFoundException('group');
        }
        if(!$this->isAuthorized($pivot->first()->user_id)){
            return false;
        }
        return $pivot->update($data);
    }



    
    
    
    
    
    
    
    
    
    
    
    
    
    
    


















    public function checkOrder(AddOrderRequest $request, Group $group, Item $item): Order {
        $orders = $group->orders->where('article', $request->article);
        if($orders->count() <= 0){
            $order = $this->createOrder($request, $group, $item);
        } else {
            $order = $orders->first();
        }
        return $order;
    }

    public function checkUser(AddOrderRequest $request, Order $order, Delivery $delivery) {
        $user = $order->users()->where(['user_id' => Auth::id()])->get()->first();
        if(!$user){
            $order = $this->attachUser($request, $order, $delivery);
        } else {
            throw new NotCreatedException('Вы уже создавали данный заказ. Заказ');
        }
    }

    protected function createOrder(AddOrderRequest $request, Group $group, Item $item): Order{
        return DB::transaction(function() use ($group, $item){
            $item = $this->addItem($item);
            $order = $this->addOrder($group, $item);
            return $order;
        });
    }

    protected function addItem(Item $item): Item {
        $item->pid = $item->info->id;
        $item->sid = $item->info->sid;
        $item->save();
        if(!$item->id){
            throw new BaseException;
        }
        return $item;
    }

    protected function addOrder(Group $group, Item $item): Order {
        $order = new Order;
        $order->group_id = $group->id;
        $order->item_id = $item->id;
        $order->save();
        if(!$order->id){
            throw new BaseException;
        }
        return $order;
    }

    /*protected function attachUser(AddOrderRequest $request, Order $order, Delivery $delivery): Order{
        $order->users()->attach(Auth::id(), $this->getUserAttachData($request, $delivery));
        if(!$order->users()->where(['user_id' => Auth::id()])->get()->first()){
            throw new BaseException;
        } 
        return $order;
    }*/

    protected function getUserAttachData(AddOrderRequest $request, Delivery $delivery): array {
        return [
            'qty' => $request->qty,
            'delivery' => $delivery->totalSum,
            'delivery_info' => $delivery,
        ];
    }






    public function create(AddOrderRequest $request){
        $group = (new Group)->findOrException($request->group);
        $orders = $group->orders->where('article', $request->article);
        if($orders->count() <= 0){
            $order = $this->createOrder($request, $group);
        } else {
            $order = $orders->first();
        }

        $user = $order->users()->where(['user_id' => Auth::id()])->get()->first();
        if(!$user){
            $order = $this->attachUser($request, $order);
        } else {
            // $user = $this->changeUser($request, $user);
            throw new NotCreatedException('Вы уже создавали данный заказ. Заказ');
            
        }
    }

    public function change(OrderUser $orderUser, ChangeOrderRequest $request){
        $pivot = $orderUser->findOrException($request->id);
        if($pivot->user_id != Auth::id() && !Auth::user()->isAdmin()){
            throw new NotAuthorizedException;
        }
        $pivot->qty = $request->qty;
        if(!$pivot->save()){
            throw new BaseException;
        }
    }

    public function delete(OrderUser $orderUser, int $id): bool{
        $pivot = $orderUser->find($id);
        if(Auth::id() != $pivot->user_id && !Auth::user()->isAdmin()){
            throw new NotAuthorizedException;
        }
        $order = $pivot->order;
        if(!$order){
            throw new NotFoundException('Заказ');
        }

        DB::transaction(function() use ($pivot, $order){
            $this->detachUser($pivot);
            if($order->users->count() <= 0){
                $this->deleteOrder($order);
            }
        });
        return true;
    }

    public function toCart(Collection $orders){
        if($orders->count() <= 0){
            throw new ItemNotFoundException;
        }
        $resp = new Resp;
        foreach($orders as $order){
            if($order->item->empty()){
                $resp->addToErrors('Пропущен неизвестный товар. Пожалуйста сверьтесь с удаленной корзиной.');
                continue;
            }

            $qty = $order->users->reduce(function($carry, $user){
                return $carry + $user->pivot->qty;
            });
            if($qty < $order->item->min_qty){
                $resp->addToErrors('Товар с артикулом '.$order->item->sid.' не добавлен в корзину, т.к. количество меньше минимального');
                continue;
            }

            $data = [
                'item_id' => $order->item->id,
                'item_sid' => $order->item->sid,
                'qty' => $qty
            ];

            $result = Curl::to($this->url.'/cart-item/')
                ->withOption('USERPWD', $this->login.':'.$this->pass)
                ->withData($data)
                ->asJson()
                ->post();

            if(!empty($result->id)){
                $resp->addToSuccess('Товар с артикулом '.$order->item->sid.' в количестве '.$result->qty.$order->item->pluralNameFormat.' успешно добавлен');
            } else {
                $resp->addToErrors('Товар с артикулом '.$order->item->sid.' не добавлен в корзину. Пожалуйста добавьте в ручную.');
            }

            if($qty != $result->qty){
                $resp->addToErrors('Количество выбранного товара с артикулом '.$order->item->sid.' отличается от количества добавленного в корзину. Пожалуйста проверьте.');
            }
        }
        return $resp;
    }

    public function toArchive(Group $group, Status $status): Group{
        $group->user_id = Auth::id();
        $group->status_id = $status->archived()->id;
        $group->processed_at = Carbon::now();
        if(!$group->save()){
            throw new BaseException;
        }
        return $group;
    }

    public function makeReport(Group $group): Report{
        $report = new Report;
        $users = collect();
        foreach($group->orders as $order){
            foreach($order->users as $user){
                $users->put($user->id, $user);
            }
        }
        foreach($users as $user){
            $report->add(new ReportItem($user, $group));
        }
        return $report;
    }

    public function createGroup(Status $status): Group{
        $group = new Group;
        $group->status_id = $status->new()->id;
        $group->save();
        if(!$group->id){
            throw new BaseException;
        }
        return $group;
    }

    

    public function changeUser(AddOrderRequest $request, User $user): User{
        $user->pivot->qty = $request->qty;
        $user->pivot->save();
        if($user->pivot->qty != $request->qty){
            throw new BaseException;
        }
        return $user;
    }

    public function detachUser(OrderUser $pivot): bool{
        if(!$pivot->delete()){
            throw new BaseException;
        }
        return true;
    }

    public function deleteOrder(Order $order): bool{
        if(!$order->delete()){
            throw new BaseException;
        }
        return true;
    }

}