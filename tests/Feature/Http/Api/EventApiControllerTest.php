<?php

namespace Feature\Http\Api;

use App\Enums\Ability;
use App\Enums\Visibility;
use App\Exceptions\Handler;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Requests\Filters\EventFilterRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\QueryBuilder\SortOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Event::class)]
#[CoversClass(EventApiController::class)]
#[CoversClass(EventFilterRequest::class)]
#[CoversClass(EventResource::class)]
#[CoversClass(Handler::class)]
#[CoversClass(SortOptions::class)]
class EventApiControllerTest extends TestCase
{
    use ActsWithToken;
    use GeneratesTestData;

    public function testEventsCanBeRequestedOnlyWithCorrectAbility(): void
    {
        $this->createCollection(Event::factory()->forLocation()->forOrganization());

        $this->assertTokenCanGetOnlyWithAbility('api/events', Ability::ViewEvents);
    }

    public function testEventsCannotBeFilteredWithInvalidFilters(): void
    {
        $this->assertTokenCannotGetDespiteAbility('api/events?filter[invalid-filter]=value', Ability::ViewEvents, Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([
                'message' => 'Invalid query',
                'errors' => [
                    'filter' => 'Requested filter(s) `invalid-filter` are not allowed. Allowed filter(s) are `search, visibility, date_from, date_until, event_series_id, organization_id, location_id, document_id, event_type`.',
                ],
            ]);
    }

    public function testEventsCannotBeFilteredWithInvalidIncludes(): void
    {
        $this->assertTokenCannotGetDespiteAbility('api/events?include=invalid-include', Ability::ViewEvents, Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([
                'message' => 'Invalid query',
                'errors' => [
                    'include' => 'Requested include(s) `invalid-include` are not allowed. Allowed include(s) are `event_series, location, organization, organization.location, parent_event, sub_events, sub_events_count`.',
                ],
            ]);
    }

    public function testEventsCannotBeFilteredWithInvalidValues(): void
    {
        $this->assertTokenCannotGetDespiteAbility('api/events?filter[organization_id]=42', Ability::ViewEvents, Response::HTTP_UNPROCESSABLE_ENTITY)
            ->assertJsonFragment([
                'message' => 'Invalid query',
                'errors' => [
                    'filter.organization_id' => [
                        'The selected Organization is invalid.',
                    ],
                ],
            ]);
    }

    public function testSinglePublicEventCanBeRequestedOnlyWithCorrectAbility(): void
    {
        $event = self::createEvent(Visibility::Public);

        $this->assertTokenCanGetOnlyWithAbility("api/events/{$event->slug}", Ability::ViewEvents);
    }

    public function testSinglePrivateEventCanBeRequestedOnlyWithCorrectAbility(): void
    {
        $event = self::createEvent(Visibility::Private);

        $this->assertTokenCannotGetDespiteAbility("api/events/{$event->slug}", Ability::ViewEvents);
        $this->assertTokenCanGetOnlyWithAbility("api/events/{$event->slug}", [Ability::ViewEvents, Ability::ViewPrivateEvents]);
    }

    public function testNotExistingEventSlugResultsInNotFound(): void
    {
        $this->assertTokenCannotGetDespiteAbility('api/events/not-existing-slug', Ability::ViewEvents, Response::HTTP_NOT_FOUND)
            ->assertJsonFragment([
                'message' => 'Event not-existing-slug do not exist.',
            ]);
    }
}
