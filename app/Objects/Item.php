<?php

namespace App\Objects;


class Item extends Base
{
    
	public function __construct($data = []){
		parent::__construct((object)$data);
	}

}
