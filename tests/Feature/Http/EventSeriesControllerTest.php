<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\EventSeriesController;
use App\Http\Requests\EventSeriesRequest;
use App\Http\Requests\Filters\EventSeriesFilterRequest;
use App\Models\EventSeries;
use App\Options\Ability;
use App\Options\EventSeriesType;
use App\Options\FilterValue;
use App\Options\Visibility;
use App\Policies\EventSeriesPolicy;
use Database\Factories\EventSeriesFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(EventSeries::class)]
#[CoversClass(EventSeriesController::class)]
#[CoversClass(EventSeriesFactory::class)]
#[CoversClass(EventSeriesFilterRequest::class)]
#[CoversClass(EventSeriesPolicy::class)]
#[CoversClass(EventSeriesRequest::class)]
#[CoversClass(EventSeriesType::class)]
#[CoversClass(FilterValue::class)]
#[CoversClass(Visibility::class)]
class EventSeriesControllerTest extends TestCase
{
    use GeneratesTestData;
    use RefreshDatabase;

    public function testEventSeriesCanBeListedWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/event-series', Ability::ViewEventSeries);
    }

    public function testPublicEventSeriesIsAccessibleByEveryone(): void
    {
        $publicEventSeries = self::createEventSeries(Visibility::Public);
        $route = "/event-series/{$publicEventSeries->slug}";

        $this->assertGuestCanGet($route);
        $this->assertUserCanGetWithAbility($route, Ability::ViewEventSeries);
        $this->assertUserCanGetWithAbility($route, Ability::ViewPrivateEventSeries);
    }

    public function testPrivateEventSeriesIsOnlyAccessibleWithCorrectAbility(): void
    {
        $privateEvent = self::createEventSeries(Visibility::Private);
        $route = "/event-series/{$privateEvent->slug}";

        $this->assertUserCanGetOnlyWithAbility($route, Ability::ViewPrivateEventSeries, false);
    }

    public function testCreateEventSeriesFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/event-series/create', Ability::CreateEventSeries);
    }

    #[DataProvider('visibilityProvider')]
    public function testEditEventSeriesFormIsAccessibleOnlyWithCorrectAbility(Visibility $visibility): void
    {
        $eventSeries = self::createEventSeries($visibility);
        $this->assertUserCanGetOnlyWithAbility("/event-series/{$eventSeries->slug}/edit", Ability::EditEventSeries);
    }
}
