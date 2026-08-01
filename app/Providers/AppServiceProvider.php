<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;

use Illuminate\Support\Facades\URL;

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
        Paginator::useBootstrapFive();

        if (
            $this->app->environment('production') ||
            request()->header('x-forwarded-proto') === 'https' ||
            isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https'
        ) {
            URL::forceScheme('https');
        }
    }
}
