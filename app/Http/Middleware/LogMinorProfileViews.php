<?php

namespace App\Http\Middleware;

use Closure;
use GeoIp2\Database\Reader;
use Illuminate\Http\Request;
use App\Models\MinorProfileView;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LogMinorProfileViews
{
    protected $routes = [
        'singleService' => [
            'parameter' => ['singleService', 'name'],
            'model_class' => \App\Models\OtherService::class,
            'type' => 'service'
        ],
        'venue' => [
            'parameter' => 'slug',
            'type' => 'venue'
        ],
        'promoter' => [
            'parameter' => 'slug',
            'type' => 'promoter'
        ]
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        $routeName = $request->route()->getName();

        if (isset($this->routes[$routeName])) {
            $config = $this->routes[$routeName];

            // Handle OtherService (artist) differently since it uses two parameters
            if ($routeName === 'singleService') {
                $model = $this->getServiceModel($request, $config);
            } else {
                $model = $request->route()->parameter($config['parameter']);
            }

            if ($model && $this->hasMinors($model)) {
                $this->logView($request, $model, $config['type']);
            }
        }

        return $response;
    }

    protected function getServiceModel($request, $config)
    {
        $serviceType = $request->route('serviceType');
        $name = $request->route('name');

        return $config['model_class']::where('services', ucfirst($serviceType))
            ->where('name', $name)
            ->first();
    }

    protected function logView($request, $model, $type)
    {
        try {
            MinorProfileView::create([
                'serviceable_id' => $model->id,
                'serviceable_type' => get_class($model),
                'profile_type' => $type,
                'ip_address' => $request->ip(),
                'user_id' => Auth::id(),
                'user_agent' => $request->userAgent(),
                'referrer_url' => $request->header('referer'),
                'geo_location' => null
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to log minor profile view', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }

    protected function hasMinors($model): bool
    {
        try {
            // Check for minors based on model type
            return match (get_class($model)) {
                'App\Models\OtherService' => $this->checkServiceForMinors($model),
                'App\Models\Venue' => $this->checkVenueForMinors($model),
                'App\Models\Promoter' => $this->checkPromoterForMinors($model),
                default => false
            };
        } catch (\Exception $e) {
            \Log::error('Error checking for minors', [
                'error' => $e->getMessage(),
                'model_type' => get_class($model)
            ]);
            return false;
        }
    }

    protected function checkServiceForMinors($service): bool
    {
        if (!$service) return false;

        return DB::table('service_user')
            ->join('users', 'users.id', '=', 'service_user.user_id')
            ->where('service_user.serviceable_id', $service->id)
            ->where('service_user.serviceable_type', 'App\Models\OtherService')
            ->where(function ($query) {
                $eighteenYearsAgo = now()->subYears(18);
                $query->whereNotNull('users.date_of_birth')
                    ->where('users.date_of_birth', '>', $eighteenYearsAgo);
            })
            ->exists();
    }

    protected function checkVenueForMinors($venue): bool
    {
        return DB::table('service_user')
            ->join('users', 'users.id', '=', 'service_user.user_id')
            ->where('service_user.serviceable_id', $venue->id)
            ->where('service_user.serviceable_type', 'App\Models\Venue')
            ->where(function ($query) {
                $eighteenYearsAgo = now()->subYears(18);
                $query->whereNotNull('users.date_of_birth')
                    ->where('users.date_of_birth', '>', $eighteenYearsAgo);
            })
            ->exists();
    }

    protected function checkPromoterForMinors($promoter): bool
    {
        return DB::table('service_user')
            ->join('users', 'users.id', '=', 'service_user.user_id')
            ->where('service_user.serviceable_id', $promoter->id)
            ->where('service_user.serviceable_type', 'App\Models\Promoter')
            ->where(function ($query) {
                $eighteenYearsAgo = now()->subYears(18);
                $query->whereNotNull('users.date_of_birth')
                    ->where('users.date_of_birth', '>', $eighteenYearsAgo);
            })
            ->exists();
    }
}