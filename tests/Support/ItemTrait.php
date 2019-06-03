<?php

namespace Tests\Support;

use App\Item;

trait ItemTrait
{

    private $item;

    public function itemTrait(): void
    {
        $this->item = Item::first();
    }

    public function getItem(): Item
    {
        return $this->item;
    }

    public function getItemById(int $id): Item
    {
        return Item::findOrFail($id);
    }
    
}