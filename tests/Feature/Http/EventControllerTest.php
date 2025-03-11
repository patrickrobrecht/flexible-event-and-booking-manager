<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\EventType;
use App\Enums\FilterValue;
use App\Enums\Visibility;
use App\Http\Controllers\EventController;
use App\Http\Requests\EventRequest;
use App\Http\Requests\Filters\EventFilterRequest;
use App\Models\Event;
use App\Policies\EventPolicy;
use Database\Factories\EventFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
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

    public function testUserCanViewEventsOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/events', Ability::ViewEvents);
    }

    public function testGuestCanViewPublicEvent(): void
    {
        $publicEvent = self::createEvent(Visibility::Public);
        $route = "/events/{$publicEvent->slug}";

        $this->assertGuestCanGet($route);
        $this->assertUserCanGetWithAbility($route, Ability::ViewEvents);
        $this->assertUserCanGetWithAbility($route, Ability::ViewPrivateEvents);
    }

    public function testUserCanViewPrivateEventOnlyWithCorrectAbility(): void
    {
        $privateEvent = self::createEvent(Visibility::Private);
        $route = "/events/{$privateEvent->slug}";

        $this->assertUserCanGetOnlyWithAbility($route, Ability::ViewPrivateEvents, false);
    }

    public function testUserCanOpenCreateEventFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/events/create', Ability::CreateEvents);
    }

    public function testUserCanStoreEventOnlyWithCorrectAbility(): void
    {
        $data = $this->generateRandomEventData();

        $this->assertUserCanPostOnlyWithAbility('events', $data, Ability::CreateEvents, null);
    }

    public function testUserCanOpenEditEventFormOnlyWithCorrectAbility(): void
    {
        $event = self::createEvent();
        $this->assertUserCanGetOnlyWithAbility("/events/{$event->slug}/edit", Ability::EditEvents);
    }

    public function testUserCanUpdateEventOnlyWithCorrectAbility(): void
    {
        $event = self::createEvent();
        $data = $this->generateRandomEventData();

        $this->assertUserCanPutOnlyWithAbility(
            "/events/{$event->slug}",
            $data,
            Ability::EditEvents,
            "/events/{$event->slug}/edit",
            "/events/{$data['slug']}/edit"
        );
    }

    private function generateRandomEventData(): array
    {
        $eventData = Event::factory()->makeOne();
        return [
            ...$eventData->toArray(),
            'started_at' => $eventData->started_at->format('Y-m-d\TH:i'),
            'finished_at' => $eventData->finished_at->format('Y-m-d\TH:i'),
            'location_id' => self::createLocation()->id,
            'organization_id' => self::createOrganization()->id,
        ];
    }
}
