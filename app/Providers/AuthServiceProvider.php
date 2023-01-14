<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Location;
use App\Models\Organization;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Models\UserRole;
use App\Policies\BookingOptionPolicy;
use App\Policies\BookingPolicy;
use App\Policies\EventPolicy;
use App\Policies\EventSeriesPolicy;
use App\Policies\LocationPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\PersonalAccessTokenPolicy;
use App\Policies\UserPolicy;
use App\Policies\UserRolePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Booking::class => BookingPolicy::class,
        BookingOption::class => BookingOptionPolicy::class,
        Event::class => EventPolicy::class,
        EventSeries::class => EventSeriesPolicy::class,
        Location::class => LocationPolicy::class,
        Organization::class => OrganizationPolicy::class,
        PersonalAccessToken::class => PersonalAccessTokenPolicy::class,
        User::class => UserPolicy::class,
        UserRole::class => UserRolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
