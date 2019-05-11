<?php

namespace App\Exceptions\Order;

use App\Exceptions\BaseException;

class OrderNotCreatedException extends BaseException
{

    public function __construct(string $message = '')
    {
        parent::__construct($this->message($message));
    }

    protected function message(string $message): string
    {
        return __('messages.order.not.stored') . ' ' . $message;
    }

}
