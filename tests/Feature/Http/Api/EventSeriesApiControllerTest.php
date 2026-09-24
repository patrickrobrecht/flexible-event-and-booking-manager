<?php

namespace Tests\Feature\Http\Api;

use App\Enums\Ability;
use App\Enums\Visibility;
use App\Models\EventSeries;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;
use Tests\Traits\GeneratesTestData;

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

    public function testSingleEventSeriesCanBeRequestedWithIncludes(): void
    {
        $eventSeries = self::createEventSeries(Visibility::Public);

        $this->withHeadersForApiRequestWithAbility(Ability::ViewEventSeries)
            ->getJson("api/event-series/{$eventSeries->slug}?include=organization")
            ->assertOk()
            ->assertJsonPath('data.organization.name', $eventSeries->organization->name);
    }

    public function testNotExistingEventSlugResultsInNotFound(): void
    {
        $this->assertTokenCannotGetDespiteAbility('api/event-series/not-existing-slug', Ability::ViewEvents, Response::HTTP_NOT_FOUND)
            ->assertJsonFragment([
                'message' => 'Event series not-existing-slug do not exist.',
            ]);
    }
}
