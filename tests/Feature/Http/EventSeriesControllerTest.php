<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\EventSeriesType;
use App\Enums\Visibility;
use App\Models\Event;
use App\Models\EventSeries;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class EventSeriesControllerTest extends TestCase
{
    public function testUserCanViewEventSeriesOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/event-series', Ability::ViewEventSeries);
    }

    /**
     * @param list<string> $expectedEventSeries
     */
    #[DataProvider('eventSeriesFilters')]
    public function testUserCanFilterEventSeries(string $filter, array $expectedEventSeries): void
    {
        $this->actingAsUserWithAbility(Ability::ViewEventSeries);

        $eventSeries = [
            'withEventAndDocumentAndPart' => self::createEventSeries(Visibility::Public, 1, 1),
            'withEventAndDocument' => self::createEventSeries(Visibility::Public, 1),
            'withoutEventAndDocument' => self::createEventSeries(Visibility::Public, 0),
        ];
        /** @var EventSeries $partOfEventSeries */
        $partOfEventSeries = $eventSeries['withEventAndDocumentAndPart']->subEventSeries->firstOrFail();
        $partOfEventSeries->update(['visibility' => Visibility::Public]);
        $eventSeries['part'] = $partOfEventSeries;

        $document = self::createDocument(fn () => $eventSeries['withEventAndDocumentAndPart']);
        self::createDocument(static fn () => $eventSeries['withEventAndDocument']);

        $this->assertFilteredList('/event-series', $filter, 'eventSeries', [
            ...$eventSeries,
            'event' => $eventSeries['withEventAndDocumentAndPart']->events->firstOrFail(),
            'document' => $document,
        ], $expectedEventSeries);
    }

    /**
     * @return array<string, array{string, list<string>}>
     */
    public static function eventSeriesFilters(): array
    {
        return [
            'specific event' => [
                'filter[event_id]={event}',
                ['withEventAndDocumentAndPart'],
            ],
            'with events' => [
                'filter[event_id]=%2B',
                ['withEventAndDocumentAndPart', 'withEventAndDocument'],
            ],
            'without events' => [
                'filter[event_id]=-',
                ['withoutEventAndDocument'],
            ],

            'specific document' => [
                'filter[document_id]={document}',
                ['withEventAndDocumentAndPart'],
            ],
            'with documents' => [
                'filter[document_id]=%2B',
                ['withEventAndDocumentAndPart', 'withEventAndDocument'],
            ],
            'without documents' => [
                'filter[document_id]=-',
                ['withoutEventAndDocument'],
            ],

            'main event series' => [
                'filter[event_series_type]=' . EventSeriesType::MainEventSeries->value,
                ['withEventAndDocumentAndPart', 'withEventAndDocument', 'withoutEventAndDocument'],
            ],
            'parts of event series' => [
                'filter[event_series_type]=' . EventSeriesType::PartOfEventSeries->value,
                ['part'],
            ],
            'event series with parts' => [
                'filter[event_series_type]=' . EventSeriesType::EventSeriesWithParts->value,
                ['withEventAndDocumentAndPart'],
            ],
            'event series without parts' => [
                'filter[event_series_type]=' . EventSeriesType::EventSeriesWithoutParts->value,
                ['withEventAndDocument', 'withoutEventAndDocument', 'part'],
            ],
        ];
    }

    public function testGuestCanViewPublicEventSeries(): void
    {
        $publicEventSeries = self::createEventSeries(Visibility::Public);
        $route = "/event-series/{$publicEventSeries->slug}";

        $this->assertGuestCanGet($route);
        $this->assertUserCanGetWithAbility($route, Ability::ViewEventSeries);
        $this->assertUserCanGetWithAbility($route, Ability::ViewPrivateEventSeries);
    }

    public function testUserCanViewPrivateEventSeriesOnlyWithCorrectAbility(): void
    {
        $privateEvent = self::createEventSeries(Visibility::Private);
        $route = "/event-series/{$privateEvent->slug}";

        $this->assertUserCanGetOnlyWithAbility($route, Ability::ViewPrivateEventSeries, false);
    }

    public function testUserCanOpenCreateEventSeriesFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/event-series/create', Ability::CreateEventSeries);
    }

    public function testUserCanStoreEventSeriesOnlyWithCorrectAbility(): void
    {
        $data = self::generateRandomEventSeriesData();

        $this->assertUserCanPostOnlyWithAbility('event-series', $data, Ability::CreateEventSeries, null);
    }

    public function testUserCanOpenEditEventSeriesFormOnlyWithCorrectAbility(): void
    {
        $eventSeries = self::createEventSeries();
        $this->assertUserCanGetOnlyWithAbility("/event-series/{$eventSeries->slug}/edit", Ability::EditEventSeries);
    }

    public function testUserCanUpdateEventSeriesOnlyWithCorrectAbility(): void
    {
        $eventSeries = self::createEventSeries();
        /** @var array{slug: string} $data */
        $data = self::generateRandomEventSeriesData();

        $this->assertUserCanPutOnlyWithAbility(
            "/event-series/{$eventSeries->slug}",
            $data,
            Ability::EditEventSeries,
            "/event-series/{$eventSeries->slug}/edit",
            "/event-series/{$data['slug']}"
        );
    }

    public function testEventSeriesCannotHaveItselfAsParent(): void
    {
        $eventSeries = self::createEventSeries(eventsCount: 0);
        $data = array_merge(self::generateRandomEventSeriesData(), [
            'organization_id' => $eventSeries->organization_id,
            'parent_event_series_id' => $eventSeries->id,
        ]);

        $this->actingAsUserWithAbility(Ability::EditEventSeries);
        $this->put("/event-series/{$eventSeries->slug}", $data)
            ->assertSessionHasErrors([
                'parent_event_series_id' => 'Der gewählte Wert für Teil der Veranstaltungsreihe ist ungültig.',
            ]);
    }

    public function testEventSeriesParentMustBelongToSameOrganizationAsSubmittedEventSeries(): void
    {
        $parentEventSeries = self::createEventSeries(eventsCount: 0);
        $otherOrganization = self::createOrganization();

        $data = array_merge(self::generateRandomEventSeriesData(), [
            'organization_id' => $otherOrganization->id,
            'parent_event_series_id' => $parentEventSeries->id,
        ]);

        $this->actingAsUserWithAbility(Ability::CreateEventSeries);
        $this->post('event-series', $data)
            ->assertSessionHasErrors([
                'parent_event_series_id' => "Teil der Veranstaltungsreihe muss zur Organisation {$otherOrganization->name} gehören.",
            ]);
    }

    public function testUserCanDeleteEventSeriesOnlyWithCorrectAbility(): void
    {
        $eventSeries = self::createEventSeries(eventsCount: 0);

        $this->assertDatabaseHas('event_series', ['id' => $eventSeries->id]);
        $this->assertUserCanDeleteOnlyWithAbility("/event-series/{$eventSeries->slug}", Ability::DestroyEventSeries, '/event-series');
        $this->assertDatabaseMissing('event_series', ['id' => $eventSeries->id]);
    }

    /**
     * @param Closure(): EventSeries $eventSeriesProvider
     */
    #[DataProvider('eventSeriesWithReferences')]
    public function testUserCannotDeleteEventSeriesBecauseOfReferences(Closure $eventSeriesProvider, string $message): void
    {
        $eventSeries = $eventSeriesProvider();

        $this->assertUserCannotDeleteDespiteAbility("/event-series/{$eventSeries->slug}", [Ability::ViewEventSeries, Ability::DestroyEventSeries], null)
            ->assertSee($message);
    }

    /**
     * @return array<int, array{Closure(): EventSeries, string}>
     */
    public static function eventSeriesWithReferences(): array
    {
        return [
            [fn () => self::createEventSeries(eventsCount: 3), 'kann nicht gelöscht werden, weil die Veranstaltungsreihe von 3 Veranstaltungen referenziert wird.'],
            [fn () => self::createEventSeries(eventsCount: 0, subEventSeriesCount: 1), 'kann nicht gelöscht werden, weil die Veranstaltungsreihe 1 Teil-Veranstaltungsreihe hat.'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function generateRandomEventSeriesData(): array
    {
        $eventData = Event::factory()->makeOne();
        return [
            ...$eventData->toArray(),
            'organization_id' => self::createOrganization()->id,
        ];
    }
}
