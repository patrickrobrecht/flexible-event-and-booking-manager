<?php

namespace Http;

use App\Http\Controllers\LocationController;
use App\Models\Location;
use App\Options\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(LocationController::class)]
class LocationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testLocationsCanBeListedWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/locations', Ability::ViewLocations);
    }

    public function testCreateLocationFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/locations/create', Ability::CreateLocations);
    }

    public function testEditLocationFormIsAccessibleOnlyWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility("/locations/{$this->createRandomLocation()->id}/edit", Ability::EditLocations);
    }

    private function createRandomLocation(): Location
    {
        return Location::factory()->create();
    }
}
