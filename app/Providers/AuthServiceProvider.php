<?php

namespace App\Providers;

use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Document;
use App\Models\DocumentReview;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Group;
use App\Models\Location;
use App\Models\Organization;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Models\UserRole;
use App\Policies\BookingOptionPolicy;
use App\Policies\BookingPolicy;
use App\Policies\DocumentPolicy;
use App\Policies\DocumentReviewPolicy;
use App\Policies\EventPolicy;
use App\Policies\EventSeriesPolicy;
use App\Policies\GroupPolicy;
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
        Document::class => DocumentPolicy::class,
        DocumentReview::class => DocumentReviewPolicy::class,
        Event::class => EventPolicy::class,
        EventSeries::class => EventSeriesPolicy::class,
        Group::class => GroupPolicy::class,
        Location::class => LocationPolicy::class,
        Organization::class => OrganizationPolicy::class,
        PersonalAccessToken::class => PersonalAccessTokenPolicy::class,
        User::class => UserPolicy::class,
        UserRole::class => UserRolePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
