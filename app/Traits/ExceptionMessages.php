<?php
/**
 * Created by PhpStorm.
 * User: Olga
 * Date: 02.11.2018
 * Time: 13:57
 */

namespace App\Traits;


trait ExceptionMessages
{
    
    protected $notFound = '';

    public function __construct()
    {
        $this->notFound = __('messages.not.found');
    }
    
    public function getNotFound(): string
    {
        return $this->notFound;
    }
    
    

}