<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Http\Controllers\StorageLocationController;
use App\Http\Requests\StorageLocationRequest;
use App\Models\StorageLocation;
use App\Policies\StorageLocationPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
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
        $data = self::makeData(StorageLocation::factory());

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
        $data = self::makeData(StorageLocation::factory());

        $editRoute = "/storage-locations/{$storageLocation->id}/edit";
        $this->assertUserCanPutOnlyWithAbility("/storage-locations/{$storageLocation->id}", $data, Ability::EditStorageLocations, $editRoute, $editRoute);
    }

    public function testUserCanDeleteStorageLocationOnlyWithCorrectAbility(): void
    {
        $storageLocation = self::createStorageLocation();

        $this->assertUserCanDeleteOnlyWithAbility("/storage-locations/{$storageLocation->id}", Ability::DestroyStorageLocations, '/storage-locations');
        $this->assertDatabaseMissing('storage_locations', ['id' => $storageLocation->id]);
    }
}
