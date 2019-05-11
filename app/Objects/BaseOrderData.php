<?php

namespace App\Objects;

use App\Services\ItemRepository;

class BaseOrderData extends Base{

    protected $delivery;
    protected $itemService;

    public function __construct($data){
        $this->itemService = new ItemRepository;
        parent::__construct($data);
    }

}