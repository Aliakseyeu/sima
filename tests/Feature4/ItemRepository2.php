<?php

namespace Tests\Feature;

use App\{
    Item,
    Order
};
use App\Repositories\ItemRepository as SimaItemRepository;
use Illuminate\Database\Eloquent\Collection;

class ItemRepository
{
	
	public function __construct()
	{
		dd('1212121');
	}

    public function findItem(int $id): Item
    {
        return Item::find($id);
    }

    public function getActualItem(): Item
    {
        $items = $this->getLastItems();
        $repository = new SimaItemRepository(false);
        foreach($items as $item){
            $search = $repository->where('sid', $item->sid);
            if($search->id){
                return $item;
            }
        }
    }

    public function getArchivedItem(): Item
    {
        $repository = new SimaItemRepository(false);
        foreach($this->getLastArchivedGroups() as $group){
            foreach($group->orders as $order){
                if($this->isAllSimilarItemsAreArchived($order)){
                    return $order->item;
                } 
            }
        }
    }
    public function getItems()
    {
    	dd('ok');
		dd($this->getRandomSids());
	}
	
	protected function getRandomSids(int $count = 20): string
	{
		return implode(',', function() use ($count){
			$sids = [];
			while($count > 0){
				$sids[] = rand(1000000, 9999999);
				$count--;
			}
			return $sids;
		});
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

    protected function getLastItems(): Collection
    {
        return Item::orderBy('id', 'DESC')->take(100)->get();
    }
    
}
