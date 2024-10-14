<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\EventController;
use App\Models\Event;
use App\Models\Location;
use App\Options\Ability;
use App\Options\Visibility;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Traits\ChecksVisibility;

#[CoversClass(EventController::class)]
class EventControllerTest extends TestCase
{
    use ChecksVisibility;
    use RefreshDatabase;

    public function testEventsCanBeListedWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/events', Ability::ViewEvents);
    }

    public function testPublicEventIsAccessibleByEveryone(): void
    {
        $publicEvent = $this->createRandomEvent(Visibility::Public);
        $route = "/events/{$publicEvent->slug}";

        $this->assertRouteAccessibleAsGuest($route);
        $this->assertRouteAccessibleWithAbility($route, Ability::ViewEvents);
        $this->assertRouteAccessibleWithAbility($route, Ability::ViewPrivateEvents);
    }

    public function testPrivateEventIsOnlyAccessibleWithCorrectAbility(): void
    {
        $privateEvent = $this->createRandomEvent(Visibility::Private);
        $route = "/events/{$privateEvent->slug}";

        $this->assertRouteForbiddenAsGuest($route);
        $this->assertRouteNotAccessibleWithoutAbility($route, Ability::ViewPrivateEvents);
        $this->assertRouteAccessibleWithAbility($route, Ability::ViewPrivateEvents);
    }

    public function testCreateEventFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/events/create', Ability::CreateEvents);
    }

    #[DataProvider('visibilityProvider')]
    public function testEditEventFormIsAccessibleOnlyWithCorrectAbility(Visibility $visibility): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility("/events/{$this->createRandomEvent($visibility)->slug}/edit", Ability::EditEvents);
    }

    private function createRandomEvent(Visibility $visibility): Event
    {
        return Event::factory()
            ->for(Location::factory()->create())
            ->visibility($visibility)
            ->create();
    }
}
