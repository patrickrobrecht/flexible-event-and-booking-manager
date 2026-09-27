<?php

namespace Tests\Feature\Http\Api;

use App\Enums\Ability;
use App\Models\Location;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;
use Tests\Traits\GeneratesTestData;

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

    public function testSingleLocationCanBeRequestedWithIncludes(): void
    {
        $organization = self::createOrganization();
        $location = $organization->location;

        $this->withHeadersForApiRequestWithAbility(Ability::ViewLocations)
            ->getJson("api/locations/{$location->id}?include=organizations")
            ->assertOk()
            ->assertJsonPath('data.organizations.0.name', $organization->name);
    }

    public function testNotExistingLocationSlugResultsInNotFound(): void
    {
        $this->assertTokenCannotGetDespiteAbility('api/locations/42', Ability::ViewEvents, Response::HTTP_NOT_FOUND)
            ->assertJsonFragment([
                'message' => 'Location 42 do not exist.',
            ]);
    }
}
