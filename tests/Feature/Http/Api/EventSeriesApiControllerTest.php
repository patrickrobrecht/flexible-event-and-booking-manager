<?php

namespace Tests\Feature\Http\Api;

use App\Enums\Ability;
use App\Enums\Visibility;
use App\Exceptions\Handler;
use App\Http\Controllers\Api\EventSeriesApiController;
use App\Http\Requests\Filters\EventSeriesFilterRequest;
use App\Http\Resources\EventSeriesResource;
use App\Models\EventSeries;
use App\Models\QueryBuilder\SortOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;
use Tests\Traits\GeneratesTestData;

#[CoversClass(EventSeries::class)]
#[CoversClass(EventSeriesApiController::class)]
#[CoversClass(EventSeriesFilterRequest::class)]
#[CoversClass(EventSeriesResource::class)]
#[CoversClass(Handler::class)]
#[CoversClass(SortOptions::class)]
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

    public function testNotExistingEventSlugResultsInNotFound(): void
    {
        $this->assertTokenCannotGetDespiteAbility('api/event-series/not-existing-slug', Ability::ViewEvents, Response::HTTP_NOT_FOUND)
            ->assertJsonFragment([
                'message' => 'Event series not-existing-slug do not exist.',
            ]);
    }
}
