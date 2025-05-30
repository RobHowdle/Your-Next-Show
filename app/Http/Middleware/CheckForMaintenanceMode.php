<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckForMaintenanceMode
{
    /**
     * The IPs that are allowed to access the application during maintenance mode.
     * 
     * @var array
     */
    protected $allowedIPs = [
        '81.99.92.105',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (app()->isDownForMaintenance()) {
            $ip = $request->ip();

            if (in_array($ip, $this->allowedIPs)) {
                return $next($request);
            }

            // You may want to customize this response as needed
            return response('The application is down for maintenance.', 503);
        }

        return $next($request);
    }
}