<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\FilterValue;
use App\Http\Controllers\LocationController;
use App\Http\Requests\Filters\LocationFilterRequest;
use App\Http\Requests\LocationRequest;
use App\Models\Event;
use App\Models\Location;
use App\Models\Organization;
use App\Policies\LocationPolicy;
use Database\Factories\LocationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Event::class)]
#[CoversClass(FilterValue::class)]
#[CoversClass(Location::class)]
#[CoversClass(LocationController::class)]
#[CoversClass(LocationFactory::class)]
#[CoversClass(LocationFilterRequest::class)]
#[CoversClass(LocationPolicy::class)]
#[CoversClass(LocationRequest::class)]
#[CoversClass(Organization::class)]
class LocationControllerTest extends TestCase
{
    use GeneratesTestData;
    use RefreshDatabase;

    public function testUserCanViewLocationsOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/locations', Ability::ViewLocations);
    }

    public function testUserCanViewCreateLocationFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/locations/create', Ability::CreateLocations);
    }

    public function testUserCanStoreLocationOnlyWithCorrectAbility(): void
    {
        $data = Location::factory()->makeOne()->toArray();

        $this->assertUserCanPostOnlyWithAbility('locations', $data, Ability::CreateLocations, null);
    }

    public function testUserCanViewEditLocationFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility("/locations/{$this->createRandomLocation()->id}/edit", Ability::EditLocations);
    }

    public function testUserCanUpdateLocationOnlyWithCorrectAbility(): void
    {
        $location = $this->createRandomLocation();
        $data = Location::factory()->makeOne()->toArray();

        $editRoute = "/locations/{$location->id}/edit";
        $this->assertUserCanPutOnlyWithAbility("/locations/{$location->id}", $data, Ability::EditLocations, $editRoute, $editRoute);
    }

    private function createRandomLocation(): Location
    {
        return self::createLocation();
    }
}
