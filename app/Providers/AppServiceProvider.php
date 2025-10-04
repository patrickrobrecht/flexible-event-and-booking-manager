<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

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
        // Use Bootstrap framework for pagination rendering
        Paginator::useBootstrapFive();

        // Use custom PersonalAccessToken implementation.
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);

        // Rate limiting
        RateLimiter::for('register', static function (Request $request) {
            return Limit::perMinute(2)->by($request->ip());
        });
    }
}
