<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\Visibility;
use App\Models\Event;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EventControllerTest extends TestCase
{
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
        $data = self::generateRandomEventData();

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
        /** @var array{slug: string} $data */
        $data = self::generateRandomEventData();

        $this->assertUserCanPutOnlyWithAbility(
            "/events/{$event->slug}",
            $data,
            Ability::EditEvents,
            "/events/{$event->slug}/edit",
            "/events/{$data['slug']}"
        );
    }

    public function testEventCannotHaveItselfAsParent(): void
    {
        $event = self::createEvent();
        $data = array_merge(self::generateRandomEventData(), [
            'organization_id' => $event->organization_id,
            'parent_event_id' => $event->id,
        ]);

        $this->actingAsUserWithAbility(Ability::EditEvents);
        $this->put("/events/{$event->slug}", $data)
            ->assertSessionHasErrors([
                'parent_event_id' => 'Der gewählte Wert für Teil der Veranstaltung ist ungültig.',
            ]);
    }

    /**
     * @param Closure(): array{data: array<string, mixed>, errors: array<string, string>} $scenario
     */
    #[DataProvider('eventCannotBeStoredWithInvalidParentOrEventSeriesReferenceCases')]
    public function testEventCannotBeStoredWithInvalidParentOrEventSeriesReference(Closure $scenario): void
    {
        ['data' => $data, 'errors' => $errors] = $scenario();

        $this->actingAsUserWithAbility(Ability::CreateEvents);
        $this->post('events', $data)
            ->assertSessionHasErrors($errors);
    }

    /**
     * @return array<string, array{Closure(): array{data: array<string, mixed>, errors: array<string, string>}}>
     */
    public static function eventCannotBeStoredWithInvalidParentOrEventSeriesReferenceCases(): array
    {
        return [
            'parent event already has a parent' => [
                function () {
                    $parentEvent = self::createEvent();
                    $childEvent = self::createChildEvent(Visibility::Public, $parentEvent);
                    return [
                        'data' => array_merge(self::generateRandomEventData(), [
                            'organization_id' => $childEvent->organization_id,
                            'parent_event_id' => $childEvent->id,
                        ]),
                        'errors' => ['parent_event_id' => 'Der gewählte Wert für Teil der Veranstaltung ist ungültig.'],
                    ];
                },
            ],
            'parent event belongs to another organization' => [
                function () {
                    $parentEvent = self::createEvent();
                    $otherOrganization = self::createOrganization();
                    return [
                        'data' => array_merge(self::generateRandomEventData(), [
                            'organization_id' => $otherOrganization->id,
                            'parent_event_id' => $parentEvent->id,
                        ]),
                        'errors' => ['parent_event_id' => "Teil der Veranstaltung muss zur Organisation {$otherOrganization->name} gehören."],
                    ];
                },
            ],
            'event series does not exist' => [
                function () {
                    $eventSeries = self::createEventSeries(eventsCount: 0);
                    $notExistingEventSeriesId = $eventSeries->id;
                    $eventSeries->delete();
                    return [
                        'data' => array_merge(self::generateRandomEventData(), [
                            'event_series_id' => $notExistingEventSeriesId,
                        ]),
                        'errors' => ['event_series_id' => 'Der gewählte Wert für Teil der Veranstaltungsreihe ist ungültig.'],
                    ];
                },
            ],
            'event series belongs to another organization' => [
                function () {
                    $eventSeries = self::createEventSeries();
                    $otherOrganization = self::createOrganization();
                    return [
                        'data' => array_merge(self::generateRandomEventData(), [
                            'organization_id' => $otherOrganization->id,
                            'event_series_id' => $eventSeries->id,
                        ]),
                        'errors' => ['event_series_id' => "Teil der Veranstaltungsreihe muss zur Organisation {$otherOrganization->name} gehören."],
                    ];
                },
            ],
        ];
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
    private static function generateRandomEventData(): array
    {
        /** @var Event $eventData */
        $eventData = Event::factory()->makeOne();
        return [
            ...$eventData->toArray(),
            /** @phpstan-ignore method.nonObject */
            'started_at' => $eventData->started_at->format('Y-m-d\TH:i'),
            /** @phpstan-ignore method.nonObject */
            'finished_at' => $eventData->finished_at->format('Y-m-d\TH:i'),
            'location_id' => self::createLocation()->id,
            'organization_id' => self::createOrganization()->id,
        ];
    }
}
