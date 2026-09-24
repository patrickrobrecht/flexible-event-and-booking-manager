<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Models\Location;
use Closure;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LocationControllerTest extends TestCase
{
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

    #[DataProvider('invalidLocationData')]
    public function testUserCannotStoreInvalidLocationDespiteAbility(Closure $dataProvider, string $errorMessage): void
    {
        $this->assertUserCannotPostDespiteAbility('locations', $dataProvider(), Ability::CreateLocations, 'locations/create', 'locations/create');
        $this->get('locations/create')
            ->assertSee($errorMessage);
    }

    /**
     * @return list<array{Closure(): array<string, mixed>, string}>
     */
    public static function invalidLocationData(): array
    {
        return [
            [
                fn () => [],
                'Name muss ausgefüllt werden, wenn Straße nicht ausgefüllt wurde.',
            ],
            [
                fn () => [
                    ...Location::factory()->makeOne()->toArray(),
                    'street' => '',
                ],
                'Straße muss ausgefüllt werden, wenn Hausnummer ausgefüllt wurde.',
            ],
            [
                fn () => [
                    ...Location::factory()->makeOne()->toArray(),
                    'city' => '',
                ],
                'Stadt muss ausgefüllt werden, wenn Postleitzahl ausgefüllt wurde.',
            ],
        ];
    }

    public function testUserCanViewLocationOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility("/locations/{$this->createRandomLocation()->id}", Ability::ViewLocations);
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
        $this->assertUserCanPutOnlyWithAbility(
            "/locations/{$location->id}",
            $data,
            Ability::EditLocations,
            $editRoute,
            '/locations'
        );
    }

    public function testUserCanDeleteLocationsOnlyWithCorrectAbility(): void
    {
        $location = self::createLocation();

        $this->assertDatabaseHas('locations', ['id' => $location->id]);
        $this->assertUserCanDeleteOnlyWithAbility("/locations/{$location->id}", Ability::DestroyLocations, '/locations');
        $this->assertDatabaseMissing('locations', ['id' => $location->id]);
    }

    /**
     * @param Closure(): Location $locationProvider
     */
    #[DataProvider('locationsWithReferences')]
    public function testUserCannotDeleteLocationBecauseOfReferences(Closure $locationProvider, string $message): void
    {
        $location = $locationProvider();

        $this->assertUserCannotDeleteDespiteAbility("/locations/{$location->id}", [Ability::ViewOrganizations, Ability::DestroyLocations], null)
            ->assertSee($message);
    }

    /**
     * @return array<int, array{Closure(): Location, string}>
     */
    public static function locationsWithReferences(): array
    {
        return [
            [fn () => self::createEvent()->location, 'kann nicht gelöscht werden, weil der Standort von 1 Veranstaltung referenziert wird.'],
            [fn () => self::createOrganization()->location, 'kann nicht gelöscht werden, weil der Standort von 1 Veranstaltung referenziert wird.'],
        ];
    }

    private function createRandomLocation(): Location
    {
        return self::createLocation();
    }
}
