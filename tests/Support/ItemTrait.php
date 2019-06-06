<?php

namespace Tests\Support;

use Ixudra\Curl\Facades\Curl;
use App\Item;

trait ItemTrait
{

    // private $item;
    // private $count;

    // public function itemTrait(): void
    // {
    //     $this->item = Item::first();
    //     $this->count = $this->getActualItemsCount();
    // }

    // public function getActualItemsCount(): int
    // {
    //     return Item::count();
    // }

    // public function getItemById(int $id): Item
    // {
    //     return Item::findOrFail($id);
    // }

    public function getActualItem(): object
    {
        return Curl::to(config('api.url').'/item/?per_page=1')
            ->withContentType('application/json')
            ->asJson()
            ->get()
            ->items[0];
    }

    // public function getItemsCount(): int
    // {
    //     return $this->count;
    // }

    // public function getItem(): Item
    // {
    //     return $this->item;
    // }
    
}