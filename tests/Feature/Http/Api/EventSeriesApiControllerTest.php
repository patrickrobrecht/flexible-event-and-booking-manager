<?php

namespace Feature\Http\Api;

use App\Enums\Ability;
use App\Enums\Visibility;
use App\Http\Controllers\Api\EventSeriesApiController;
use App\Http\Requests\Filters\EventSeriesFilterRequest;
use App\Models\EventSeries;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;
use Tests\Traits\GeneratesTestData;

#[CoversClass(EventSeriesApiController::class)]
#[CoversClass(EventSeriesFilterRequest::class)]
class EventSeriesApiControllerTest extends TestCase
{
    use ActsWithToken;
    use GeneratesTestData;

    public function testEventSeriesCanBeRequestedOnlyWithCorrectAbility(): void
    {
        $this->createCollection(EventSeries::factory()->forOrganization());

        $this->assertTokenCanGetOnlyWithAbility('api/event-series', Ability::ViewEventSeries);
    }

    public function testSinglePublicEventSeriesCanBeRequestedOnlyWithCorrectAbility(): void
    {
        $eventSeries = self::createEventSeries(Visibility::Public);

        $this->assertTokenCanGetOnlyWithAbility("api/event-series/{$eventSeries->slug}", Ability::ViewEventSeries);
    }

    public function testSinglePrivateEventSeriesCanBeRequestedOnlyWithCorrectAbility(): void
    {
        $eventSeries = self::createEventSeries(Visibility::Private);

        $this->assertTokenCannotGetDespiteAbility("api/event-series/{$eventSeries->slug}", Ability::ViewEventSeries);
        $this->assertTokenCanGetOnlyWithAbility("api/event-series/{$eventSeries->slug}", [Ability::ViewEventSeries, Ability::ViewPrivateEventSeries]);
    }
}
