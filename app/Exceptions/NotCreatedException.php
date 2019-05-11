<?php
/**
 * Created by PhpStorm.
 * User: Olga
 * Date: 21.03.2017
 * Time: 19:02
 */

namespace App\Exceptions;


class NotCreatedException extends BaseException
{

    public function __construct(string $key)
    {
        parent::__construct($this->generateMessage('created', $key));
    }

}