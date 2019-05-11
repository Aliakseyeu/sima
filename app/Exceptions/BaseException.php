<?php
/**
 * Created by PhpStorm.
 * User: Olga
 * Date: 21.03.2017
 * Time: 19:02
 */

namespace App\Exceptions;


class BaseException extends \Exception
{

    public function __construct(string $message)
    {
        parent::__construct($message);
    }

    protected function generateMessage(string $key, string $name): string
    {
        return __('messages.not.'.$key, ['name'=>__('messages.'.$name)]);
    }

}