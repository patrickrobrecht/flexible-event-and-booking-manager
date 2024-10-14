<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\EventSeriesController;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Location;
use App\Options\Ability;
use App\Options\Visibility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Traits\ChecksVisibility;

#[CoversClass(EventSeriesController::class)]
class EventSeriesControllerTest extends TestCase
{
    use ChecksVisibility;
    use RefreshDatabase;

    public function testEventSeriesCanBeListedWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/event-series', Ability::ViewEventSeries);
    }

    public function testPublicEventSeriesIsAccessibleByEveryone(): void
    {
        $publicEventSeries = $this->createRandomEventSeries(Visibility::Public);
        $route = "/event-series/{$publicEventSeries->slug}";

        $this->assertRouteAccessibleAsGuest($route);
        $this->assertRouteAccessibleWithAbility($route, Ability::ViewEventSeries);
        $this->assertRouteAccessibleWithAbility($route, Ability::ViewPrivateEventSeries);
    }

    public function testPrivateEventSeriesIsOnlyAccessibleWithCorrectAbility(): void
    {
        $privateEvent = $this->createRandomEventSeries(Visibility::Private);
        $route = "/event-series/{$privateEvent->slug}";

        $this->assertRouteForbiddenAsGuest($route);
        $this->assertRouteNotAccessibleWithoutAbility($route, Ability::ViewPrivateEventSeries);
        $this->assertRouteAccessibleWithAbility($route, Ability::ViewPrivateEventSeries);
    }

    public function testCreateEventSeriesFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/event-series/create', Ability::CreateEventSeries);
    }

    #[DataProvider('visibilityProvider')]
    public function testEditEventSeriesFormIsAccessibleOnlyWithCorrectAbility(Visibility $visibility): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility("/event-series/{$this->createRandomEventSeries($visibility)->slug}/edit", Ability::EditEventSeries);
    }

    private function createRandomEventSeries(Visibility $visibility): EventSeries
    {
        return EventSeries::factory()
            ->has(
                Event::factory()
                    ->for(Location::factory()->create())
                    ->count(fake()->numberBetween(1, 5))
            )
            ->visibility($visibility)
            ->create();
    }
}
