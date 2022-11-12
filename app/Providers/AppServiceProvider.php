<?php

namespace App\Providers;

use App\Models\PersonalAccessToken;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;
use Laravel\Sanctum\Sanctum;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        // Use Bootstrap framework for pagination rendering
        Paginator::useBootstrap();

        // Use custom PersonalAccessToken implementation.
        Sanctum::usePersonalAccessTokenModel(PersonalAccessToken::class);
    }
}
