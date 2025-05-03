<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Http\Controllers\StorageLocationController;
use App\Http\Requests\StorageLocationRequest;
use App\Models\StorageLocation;
use App\Policies\StorageLocationPolicy;
use Closure;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(StorageLocation::class)]
#[CoversClass(StorageLocationController::class)]
#[CoversClass(StorageLocationPolicy::class)]
#[CoversClass(StorageLocationRequest::class)]
class StorageLocationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewStorageLocationsOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/storage-locations', Ability::ViewStorageLocations);
    }

    public function testUserCanViewSingleStorageLocationOnlyWithCorrectAbility(): void
    {
        $storageLocation = self::createStorageLocation();
        $this->assertUserCanGetOnlyWithAbility("/storage-locations/{$storageLocation->id}", Ability::ViewStorageLocations);
    }

    public function testUserCanOpenCreateStorageLocationFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/storage-locations/create', Ability::CreateStorageLocations);
    }

    public function testUserCanStoreStorageLocationOnlyWithCorrectAbility(): void
    {
        $data = self::makeStorageLocationData();

        $this->assertUserCanPostOnlyWithAbility('/storage-locations', $data, Ability::CreateStorageLocations, null);
    }

    public function testUserCanOpenEditStorageLocationFormOnlyWithCorrectAbility(): void
    {
        $storageLocation = self::createStorageLocation();
        $this->assertUserCanGetOnlyWithAbility("/storage-locations/{$storageLocation->id}/edit", Ability::EditStorageLocations);
    }

    public function testUserCanUpdateStorageLocationOnlyWithCorrectAbility(): void
    {
        $storageLocation = self::createStorageLocation();
        $data = self::makeStorageLocationData();

        $editRoute = "/storage-locations/{$storageLocation->id}/edit";
        $this->assertUserCanPutOnlyWithAbility("/storage-locations/{$storageLocation->id}", $data, Ability::EditStorageLocations, $editRoute, $editRoute);
    }

    public function testUserCanDeleteStorageLocationOnlyWithCorrectAbility(): void
    {
        $storageLocation = self::createStorageLocation();

        $this->assertUserCanDeleteOnlyWithAbility("/storage-locations/{$storageLocation->id}", Ability::DestroyStorageLocations, '/storage-locations');
        $this->assertDatabaseMissing('storage_locations', ['id' => $storageLocation->id]);
    }

    /**
     * @param Closure(): StorageLocation $storageLocationProvider
     */
    #[DataProvider('storageLocationsWithReferences')]
    public function testUserCannotDeleteOrganizationBecauseOfReferences(Closure $storageLocationProvider, string $message): void
    {
        $storageLocations = $storageLocationProvider();

        $this->assertUserCannotDeleteDespiteAbility("/storage-locations/{$storageLocations->id}", [Ability::ViewOrganizations, Ability::DestroyOrganizations], null)
            ->assertSee($message);
    }

    /**
     * @return array<int, array{Closure(): StorageLocation, string}>
     */
    public static function storageLocationsWithReferences(): array
    {
        return [
            [fn () => self::createStorageLocation(childStorageLocationsCount: 1), 'kann nicht gelöscht werden, weil der Lagerplatz einen untergeordneten Lagerplatz besitzt.'],
            [fn () => self::createStorageLocation(childStorageLocationsCount: 3), 'kann nicht gelöscht werden, weil der Lagerplatz 3 untergeordnete Lagerplätze besitzt.'],
            [fn () => self::createStorageLocation(materialsCount: 1), 'kann nicht gelöscht werden, weil der Lagerplatz 1 Material enthält.'],
            [fn () => self::createStorageLocation(materialsCount: 3), 'kann nicht gelöscht werden, weil der Lagerplatz 3 Materialien enthält.'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function makeStorageLocationData(): array
    {
        return [
            ...self::makeData(StorageLocation::factory()),
            'parent_storage_location_id' => self::createStorageLocation()->id,
        ];
    }
}
