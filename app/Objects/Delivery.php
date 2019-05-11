<?php

namespace App\Objects;


class Delivery extends Base
{

    public function __construct($data = []){
        parent::__construct((object)$data);
    }

    public function __get($key){
        return (float) parent::__get($key);
    }

    public function renderPrice(): string{
        if(isset($this->data->totalSum)){
            return $this->data->totalSum . ' ' . $this->data->currency;
        }
        return '?';
    }

    public function getPrice(): float
    {
        return (float) $this->totalSum;
    }
    
}
