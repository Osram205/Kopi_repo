<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        if (!app()->runningInConsole()) {
            if (request()->header('X-Forwarded-Proto') === 'https' || env('APP_ENV') === 'production' || str_contains(env('APP_URL'), 'devtunnels.ms')) {
                \Illuminate\Support\Facades\URL::forceScheme('https');
                \Illuminate\Support\Facades\URL::forceRootUrl(rtrim(env('APP_URL'), '/'));
            }
        }
    }
}
