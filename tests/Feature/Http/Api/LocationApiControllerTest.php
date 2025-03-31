<?php

namespace Feature\Http\Api;

use App\Enums\Ability;
use App\Http\Controllers\Api\LocationApiController;
use App\Http\Requests\Filters\LocationFilterRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use App\Models\QueryBuilder\SortOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;
use Tests\Traits\GeneratesTestData;

#[CoversClass(LocationApiController::class)]
#[CoversClass(LocationFilterRequest::class)]
#[CoversClass(LocationResource::class)]
#[CoversClass(SortOptions::class)]
class LocationApiControllerTest extends TestCase
{
    use ActsWithToken;
    use GeneratesTestData;

    public function testLocationsCanBeRequestedOnlyWithCorrectAbility(): void
    {
        $this->createCollection(Location::factory());

        $this->assertTokenCanGetOnlyWithAbility('api/locations', Ability::ViewLocations);
    }

    public function testSingleLocationCanBeRequestedOnlyWithCorrectAbility(): void
    {
        $location = self::createLocation();

        $this->assertTokenCanGetOnlyWithAbility("api/locations/{$location->id}", Ability::ViewLocations);
    }
}
