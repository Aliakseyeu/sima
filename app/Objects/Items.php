<?php

namespace App\Objects;

use App\Objects\Item;

class Items
{
    
	protected $items;
	
	public function __construct(){
		$this->items = collect();
	}
	
	public function add(Item $item, $key = 'sid'){
		$this->items->put($item->$key, $item);
	}
	
	public function all(){
		return $this->items;
	}

	public function get(string $key): Item {
		return $this->items->has($key) ? $this->items->get($key) : new Item;
	}
	
}
