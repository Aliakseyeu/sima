<?php

namespace App\Repositories;

use App\Exceptions\NotFoundException;
use Ixudra\Curl\Facades\Curl;
use App\Objects\{Item};

class ItemRepository
{

    protected $exception = true;

    public function __construct($exception = true){
        $this->exception = $exception;
    }

    public function find(int $id): Item {
        $data = $this->getItemInfo($this->getItemUrl($id));
        return new Item($this->tryToGetItemInfo($data));
    }

    public function where(string $column, string $value): Item {
        $data = $this->getItemInfo($this->getByUrl($column, $value));
        return new Item($this->tryToGetItemInfo($data));
    }

    protected function getByUrl(string $key, string $value): string {
        return config('api.url').'/item/?with_adult=1&'.$key.'='.$value;
    }

    protected function getItemUrl(int $id): string {
        return config('api.url').'/item/'.$id.'/?with_adult=1';
    }

    protected function getItemInfo(string $url) {
        return Curl::to($url)->withContentType('application/json')->asJson()->get();
    }

    protected function tryToGetItemInfo($data) {
        try{
            if($data && !empty($data->id)){
                return $data;
            }
            return $data->items[0];
        } catch (\Exception $e){
            if($this->exception){
                throw new NotFoundException('item');
            }
            return false;
        }
    }












    

    public function getDeliveryPrice(array $data): object {
        $user = $this->getDeliveryAddress();
        $delivery = Curl::to($this->getDeliveryPriceUrl())
            ->withData($this->getDeliveryData($user, $data))
            ->asJson()
            ->post();
        dd($delivery);
        return $delivery;
    }

    protected function getDeliveryData(object $user, array $data): array {
        return [
            'settlement_id' => $user->settlement_id,
            'items' => $data,
        ];
    }

    protected function getDeliveryPriceUrl(): string {
        return $this->url.'/delivery-price/';
    }

    public function getDeliveryAddress(): object {
        $user = Curl::to($this->getDeliveryAddressUrl())
            ->withContentType('application/json')
            ->withOption('USERPWD', $this->login.':'.$this->pass)
            ->asJson()
            ->get();
        try {
            return $user->items[0];
        } catch (\Exception $e){
            throw new UserNotFoundException;
        }
    }

    protected function getDeliveryAddressUrl(): string {
        return $this->url.'/user-delivery-address/';
    }




    public function addItemsInfo(Collection $orders): Collection{
        if($orders->count() <= 0){
            return $orders;
        }
        $articles = $orders->implode('article', ',');
        $items = $this->getByArticle($articles);
        $orders->map(function($order) use ($items){
            $order->item = $items->get($order->article);
        });
        return $orders;
    }
    
	/*public function getByArticle(string $article){
        $items = new Items;
        if(!$article){
            return $items;
        }
        try{
            $url = $this->url.'/item/?sid='.trim($article).'&per_page=100';
            do{
                $data = Curl::to($url)->withContentType('application/json')->asJson()->get();
                foreach($data->items as $item){
                    $item = new Item($item);
                    $items->add($item);
                }
            } while (!empty($data->_links->next->href) && $url = $data->_links->next->href);
        } catch(\Exception $e) {
            throw new ItemNotFoundException();
            if(Request::ajax()){
                return false;
            }
        }
        return $items;
	}*/

	public function addDeliveryPrice(Collection $orders){
        $orders->each(function($order){
            if(!$order->item->empty() || true){
                $order->users->each(function($user) use ($order){
                    $user->pivot->delivery = new Delivery($this->getDeliveryPrice(['item_id' => $order->item->id, 'qty' => $user->pivot->qty]));
                });
            }
        });
        /*dd($orders);
        foreach($orders as $order){
            if($order->item->empty()){
                continue;
            }
            // $order->delivery = $this->getDeliveryPrice(['item_id' => $order->item->id, 'qty' => 1]);
            // continue;
            $item = $order->item;
            foreach($order->getRelations()['users'] as $user){
                // dd($user->getRelations()['pivot']);
                $user->getRelations()['pivot']->delivery = $this->getDeliveryPrice(['item_id' => $item->id, 'qty' => $user->pivot->qty]);
            }
        }*/
        // dd($orders);
        return $orders;
    }
    
    // public function getDeliveryPrice(array $data){
    //     // return (object)[];
    //     $user = $this->getDeliveryAddress();
    //     $data = [
    //         'settlement_id' => $user->settlement_id,
    //         'items' => $data,
    //     ];
    //     $delivery = Curl::to($this->url.'/delivery-price/')
    //         ->withData($data)
    //         ->asJson()
    //         ->post();
    //     return $delivery;
    // }

    // public function getDeliveryAddress(){
    //     $user = Curl::to($this->url.'/user-delivery-address/')
    //         ->withContentType('application/json')
    //         ->withOption('USERPWD', $this->login.':'.$this->pass)
    //         ->asJson()
    //         ->get();
    //     try {
    //         return $user->items[0];
    //     } catch (\Exception $e){
    //         return new Base();
    //         // dd($user);
    //         throw new UserNotFoundException;
    //     }
    // }

	public function getItemQtyData($items, $orders){
		$data = [];
		foreach($orders as $order){
			if(!$items->all()->has($order->article)){
				continue;
			}
			$temp['item_id'] = $items->all()->get($order->article)->id;
			foreach($order->users as $user){
				$temp['qty'] = $user->pivot->qty;
                $data[] = $temp;
			}
		}
		return $data;
	}
	
}
