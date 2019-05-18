<?php

namespace Tests\Feature;

use App\{
    Item,
    Order
};
use Ixudra\Curl\Facades\Curl;
use Illuminate\Database\Eloquent\Collection;

class BaseOrder extends BaseGroup
{
	
	private $item;
	private $order;
	
	public function setUp(): void
	{
		parent::setUp();
		$this->item = $this->createItem();
		$this->attachUser();
	}
	
	public function createItem(): Item
	{
		$findedItem = $this->findItem();
		$this->order = $this->createOrder();
		
		$item = new Item();
		$item->order_id = $this->order->id;
		$item->pid = $findedItem->id;
		$item->sid = $findedItem->sid;
		$item->info = json_encode($findedItem);
		$item->save();
		return $item;
	}
	
	public function attachUser(int $count = 1): void
	{
		for($i = 0; $i < $count; $i++){
			$qty = $this->getQty();
			$delivery = $this->getDeliveryPrice($this->item->pid, $qty);
		    $this->order->users()->attach(
		        $this->createUser(), 
		        ['qty' => $qty, 'delivery' => $delivery->totalSum, 'delivery_info' => json_encode($delivery)]
		    );
		}
	}
	
	public function createOrder(): Order
	{
		$order = new Order();
		$order->group_id = $this->getGroup()->id;
		$order->save();
		return $order;
	}
	
	public function findItem(): object
	{
		$sids = $this->getRandomSids();
	    do{
	    	$data = Curl::to(config('api.url').'/item/?with_adult=1&sid='.$sids)
			    ->withContentType('application/json')
			    ->asJson()
			    ->get();
//			sleep(1);
		} while(!is_array($data->items) || count($data->items) <= 0);
		return ($data->items)[0];
	}
	
	public function getItemById(int $id): Item
	{
		return Item::find($id);
	}
	
	public function getQty(): int
	{
		return rand(1, 10);
	}
	
	public function getOrdersCount(): int
	{
	    return Order::count();
	}
	
	public function getLastOrder(): Order
	{
		return Order::all()->last();
	}
	
	protected function getDeliveryPrice(int $id, int $qty): object
	{
	    return Curl::to(config('api.url').'/delivery-price/')
            ->withData($this->getDeliveryData(193824312, $id, $qty))
            ->asJson()
            ->post();
	}
	
	protected function getDeliveryData(int $settlement, int $id, int $qty): array {
        return [
            'settlement_id' => $settlement,
            'items' => ['item_id' => $id, 'qty' => $qty]
        ];
    }
	
	protected function getRandomSids(int $count = 50): string
	{
		$sids = [];
		while($count > 0){
			$sids[] = rand(1000000, 9999999);
			$count--;
		}
		return implode(',', $sids);
	}
	
	
	public function getItem(): Item
	{
		return $this->item;
	}
	
	public function getOrder(): Order
	{
	    return $this->order;
	}
	
	public function tearDown(): void
    {
		parent::tearDown();
//		$this->order->users()->sync([]);
// todo pivot truncate
        $this->order->users->each->delete();
		$this->order->truncate();
		$this->item->truncate();
	}
	
//	public function __construct()
//	{
//		dd('1212121');
//	}
//
//    public function findItem(int $id): Item
//    {
//        return Item::find($id);
//    }
//
//    public function getActualItem(): Item
//    {
//        $items = $this->getLastItems();
//        $repository = new SimaItemRepository(false);
//        foreach($items as $item){
//            $search = $repository->where('sid', $item->sid);
//            if($search->id){
//                return $item;
//            }
//        }
//    }
//
//    public function getArchivedItem(): Item
//    {
//        $repository = new SimaItemRepository(false);
//        foreach($this->getLastArchivedGroups() as $group){
//            foreach($group->orders as $order){
//                if($this->isAllSimilarItemsAreArchived($order)){
//                    return $order->item;
//                } 
//            }
//        }
//    }
//    public function getItems()
//    {
//    	dd('ok');
//		dd($this->getRandomSids());
//	}
//	

//
//    protected function isAllSimilarItemsAreArchived(Order $order): bool
//    {
//        foreach(Item::whereSid($order->item->sid)->get() as $item){
//            if($item->order->group->status->slug != 'archived'){
//                return false;
//            }
//        }
//        return true;
//    }
//
//    protected function getLastItems(): Collection
//    {
//        return Item::orderBy('id', 'DESC')->take(100)->get();
//    }
    
}
