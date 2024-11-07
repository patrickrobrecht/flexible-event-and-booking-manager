<?php

namespace Http;

use App\Http\Controllers\LocationController;
use App\Http\Requests\Filters\LocationFilterRequest;
use App\Http\Requests\LocationRequest;
use App\Models\Location;
use App\Options\Ability;
use App\Options\FilterValue;
use App\Policies\LocationPolicy;
use Database\Factories\LocationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(FilterValue::class)]
#[CoversClass(Location::class)]
#[CoversClass(LocationController::class)]
#[CoversClass(LocationFactory::class)]
#[CoversClass(LocationFilterRequest::class)]
#[CoversClass(LocationPolicy::class)]
#[CoversClass(LocationRequest::class)]
class LocationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLocationsCanBeListedWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility('/locations', Ability::ViewLocations);
    }

    public function testCreateLocationFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility('/locations/create', Ability::CreateLocations);
    }

    public function testEditLocationFormIsAccessibleOnlyWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility("/locations/{$this->createRandomLocation()->id}/edit", Ability::EditLocations);
    }

    private function createRandomLocation(): Location
    {
        return Location::factory()->create();
    }
}
