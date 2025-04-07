<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\EventSeriesType;
use App\Enums\FilterValue;
use App\Enums\Visibility;
use App\Http\Controllers\EventSeriesController;
use App\Http\Requests\EventSeriesRequest;
use App\Http\Requests\Filters\EventSeriesFilterRequest;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Policies\EventSeriesPolicy;
use Database\Factories\EventSeriesFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Document::class)]
#[CoversClass(Event::class)]
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

    public function testUserCanViewEventSeriesOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/event-series', Ability::ViewEventSeries);
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
        $data = $this->generateRandomEventSeriesData();

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
        $data = $this->generateRandomEventSeriesData();

        $this->assertUserCanPutOnlyWithAbility(
            "/event-series/{$eventSeries->slug}",
            $data,
            Ability::EditEventSeries,
            "/event-series/{$eventSeries->slug}/edit",
            "/event-series/{$data['slug']}/edit"
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function generateRandomEventSeriesData(): array
    {
        $eventData = Event::factory()->makeOne();
        return [
            ...$eventData->toArray(),
            'organization_id' => self::createOrganization()->id,
        ];
    }
}
