<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode as Middleware;

class CheckForMaintenanceMode extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array
     */
    protected $except = [
        //
    ];
    
    protected $ips = [
        '46.56.230.35'
    ];

    public function handle($request, Closure $next)
    {
        if ($this->app->isDownForMaintenance() &&
            !in_array($request->ip(), $this->ips))
        {
            return response('Сайт закрыт на техобслуживание!', 503);
        }

        return $next($request);
    }
    
}
