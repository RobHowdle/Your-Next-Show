<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Check if the application is in maintenance mode
        if ($this->app->isDownForMaintenance()) {
            $request = request();
            $allowedIp = '81.99.92.105';

            // Check if the request IP matches your allowed IP
            if ($request->ip() !== $allowedIp) {
                throw new ServiceUnavailableHttpException();
            }
        }
    }
}
