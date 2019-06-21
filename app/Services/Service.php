<?php

namespace App\Services;

use Auth;
use App\Exceptions\NotAuthorizedException;

class Service
{

    /**
     * @param int $user
     * @return bool
     * @throws NotAuthorizedException
     */
    protected function isAuthorized(int $user = 0): bool
    {
        if((Auth::user() && Auth::user()->isAdmin()) || $user == Auth::id()){
            return true;
        }
        throw new NotAuthorizedException();
    }
	
}
