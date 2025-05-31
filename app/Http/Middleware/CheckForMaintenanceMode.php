<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckForMaintenanceMode
{
    /**
     * The IPs that are allowed to access the application.
     * 
     * @var array
     */
    protected $allowedIPs = [
        '81.99.92.105',
        '192.168.65.1',
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Force debug output to a specific file
        file_put_contents(
            storage_path('logs/maintenance-ip.log'),
            date('Y-m-d H:i:s') . ' - IP: ' . $request->ip() . "\n",
            FILE_APPEND
        );

        // Check multiple IP sources
        $ip = $request->ip();
        $forwardedIp = $request->header('X-Forwarded-For');
        if ($forwardedIp) {
            $ipParts = explode(',', $forwardedIp);
            $forwardedIp = trim($ipParts[0]);
        }

        // Check against both raw and forwarded IPs
        if (file_exists(storage_path('framework/down'))) {
            $isAllowed = in_array($ip, $this->allowedIPs) ||
                ($forwardedIp && in_array($forwardedIp, $this->allowedIPs));

            // Log result
            file_put_contents(
                storage_path('logs/maintenance-ip.log'),
                date('Y-m-d H:i:s') . ' - Maintenance Mode: ' .
                    ($isAllowed ? 'ALLOWED' : 'DENIED') . ' - IP: ' . $ip .
                    ($forwardedIp ? ' Forwarded IP: ' . $forwardedIp : '') . "\n",
                FILE_APPEND
            );

            if ($isAllowed) {
                return $next($request);
            }

            // Show maintenance mode
            $maintenanceData = file_get_contents(storage_path('framework/down'));
            $data = json_decode($maintenanceData, true) ?: [];

            return response()->view('errors.503', ['data' => $data], 503);
        }

        return $next($request);
    }
}