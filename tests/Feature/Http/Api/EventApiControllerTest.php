<?php

namespace Feature\Http\Api;

use App\Enums\Ability;
use App\Enums\Visibility;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Requests\Filters\EventFilterRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use App\Models\QueryBuilder\SortOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;
use Tests\Traits\GeneratesTestData;

#[CoversClass(EventApiController::class)]
#[CoversClass(EventFilterRequest::class)]
#[CoversClass(EventResource::class)]
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
}
