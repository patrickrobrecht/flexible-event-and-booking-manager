<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\EventController;
use App\Http\Requests\EventRequest;
use App\Http\Requests\Filters\EventFilterRequest;
use App\Models\Event;
use App\Options\Ability;
use App\Options\EventType;
use App\Options\FilterValue;
use App\Options\Visibility;
use App\Policies\EventPolicy;
use Database\Factories\EventFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Event::class)]
#[CoversClass(EventController::class)]
#[CoversClass(EventFactory::class)]
#[CoversClass(EventFilterRequest::class)]
#[CoversClass(EventPolicy::class)]
#[CoversClass(EventRequest::class)]
#[CoversClass(EventType::class)]
#[CoversClass(FilterValue::class)]
#[CoversClass(Visibility::class)]
class EventControllerTest extends TestCase
{
    use GeneratesTestData;
    use RefreshDatabase;

    public function testEventsCanBeListedWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility('/events', Ability::ViewEvents);
    }

    public function testPublicEventIsAccessibleByEveryone(): void
    {
        $publicEvent = self::createEvent(Visibility::Public);
        $route = "/events/{$publicEvent->slug}";

        $this->assertRouteAccessibleAsGuest($route);
        $this->assertRouteAccessibleWithAbility($route, Ability::ViewEvents);
        $this->assertRouteAccessibleWithAbility($route, Ability::ViewPrivateEvents);
    }

    public function testPrivateEventIsOnlyAccessibleWithCorrectAbility(): void
    {
        $privateEvent = self::createEvent(Visibility::Private);
        $route = "/events/{$privateEvent->slug}";

        $this->assertRouteForbiddenAsGuest($route);
        $this->assertRouteNotAccessibleWithoutAbility($route, Ability::ViewPrivateEvents);
        $this->assertRouteAccessibleWithAbility($route, Ability::ViewPrivateEvents);
    }

    public function testCreateEventFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility('/events/create', Ability::CreateEvents);
    }

    #[DataProvider('visibilityProvider')]
    public function testEditEventFormIsAccessibleOnlyWithCorrectAbility(Visibility $visibility): void
    {
        $event = self::createEvent($visibility);
        $this->assertRouteOnlyAccessibleWithAbility("/events/{$event->slug}/edit", Ability::EditEvents);
    }
}
