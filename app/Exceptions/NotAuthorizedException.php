<?php
/**
 * Created by PhpStorm.
 * User: Admin
 * Date: 19.03.17
 * Time: 19:03
 */

namespace App\Exceptions;

use App\Exceptions\BaseException;

class NotAuthorizedException extends BaseException
{

    public function __construct()
    {
        parent::__construct(__('messages.not.authorized'));
    }

}