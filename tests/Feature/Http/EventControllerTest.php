<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\EventType;
use App\Enums\FilterValue;
use App\Enums\Visibility;
use App\Http\Controllers\EventController;
use App\Http\Requests\EventRequest;
use App\Http\Requests\Filters\EventFilterRequest;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Policies\EventPolicy;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Document::class)]
#[CoversClass(Event::class)]
#[CoversClass(EventController::class)]
#[CoversClass(EventFilterRequest::class)]
#[CoversClass(EventPolicy::class)]
#[CoversClass(EventRequest::class)]
#[CoversClass(EventSeries::class)]
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

    public function testUserCanDeleteEventsOnlyWithCorrectAbility(): void
    {
        $event = self::createEvent();
        self::createGroups($event, 2);

        $this->assertDatabaseHas('events', ['id' => $event->id]);
        $this->assertUserCanDeleteOnlyWithAbility("/events/{$event->slug}", Ability::DestroyEvents, '/events');
        $this->assertDatabaseMissing('events', ['id' => $event->id]);
    }

    /**
     * @param Closure(): Event $eventProvider
     */
    #[DataProvider('eventsWithReferences')]
    public function testUserCannotDeleteEventsBecauseOfReferences(Closure $eventProvider, string $message): void
    {
        $event = $eventProvider();

        $this->assertUserCannotDeleteDespiteAbility("/events/{$event->slug}", [Ability::ViewEvents, Ability::DestroyEvents], null)
            ->assertSee($message);
    }

    /**
     * @return array<int, array{Closure(): Event, string}>
     */
    public static function eventsWithReferences(): array
    {
        return [
            [fn () => self::createEvent(subEventsCount: 3), 'kann nicht gelöscht werden, weil die Veranstaltung 3 Teil-Veranstaltungen hat.'],
            [fn () => self::createBooking()->bookingOption->event, 'kann nicht gelöscht werden, weil die Veranstaltung 1 Anmeldeoption hat.'],
            [fn () => self::createBookingOptionForEvent()->event, 'kann nicht gelöscht werden, weil die Veranstaltung 1 Anmeldeoption hat.'],
            [fn () => self::createEventWithBookingOptions(bookingOptionCount: 3), 'kann nicht gelöscht werden, weil die Veranstaltung 3 Anmeldeoptionen hat.'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
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
