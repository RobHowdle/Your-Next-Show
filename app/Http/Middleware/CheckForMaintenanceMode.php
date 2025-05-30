<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as BaseMaintenance;
use Illuminate\Http\Request;

class CheckForMaintenanceMode extends BaseMaintenance
{
    /**
     * The IPs that are allowed to access the application during maintenance mode.
     * 
     * @var array
     */
    protected $except = [
        // This array needs to be empty for our custom logic to work
    ];

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
    public function handle($request, Closure $next)
    {
        if ($this->app->isDownForMaintenance()) {
            $ip = $request->ip();

            if (in_array($ip, $this->allowedIPs)) {
                // Log that we're allowing this IP
                \Log::info("Allowing maintenance mode access to IP: {$ip}");
                return $next($request);
            }

            // Default Laravel maintenance response
            return $this->handleMaintenanceMode($request);
        }

        return $next($request);
    }

    /**
     * Create a response for maintenance mode.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function handleMaintenanceMode(Request $request)
    {
        $maintenanceMode = file_get_contents(storage_path('framework/down'));
        $data = json_decode($maintenanceMode, true);

        return response(
            view('errors.503', ['data' => $data]),
            503
        );
    }
}