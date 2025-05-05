<?php

namespace Tests\Feature\Livewire\StorageLocations;

use App\Livewire\StorageLocations\SelectParentStorageLocation;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(SelectParentStorageLocation::class)]
class SelectParentStorageLocationTest extends TestCase
{
    public function testComponentRendersCorrectly(): void
    {
        Livewire::test(SelectParentStorageLocation::class, ['storageLocation' => self::createStorageLocation(), 'selectedStorageLocation' => null])
            ->assertOk();
    }

    public function testSelectStorageLocationRemovesPathElementsWhenIdIsNull(): void
    {
        $grantParentStorageLocation = self::createStorageLocation();
        $parentStorageLocation = self::createStorageLocation($grantParentStorageLocation);
        $storageLocation = self::createStorageLocation($parentStorageLocation);

        $siblingOfParentStorageLocation = self::createStorageLocation($grantParentStorageLocation);
        $siblingStorageLocation = self::createStorageLocation($parentStorageLocation);
        $anotherRootStorageLocation = self::createStorageLocation();

        $component = Livewire::test(SelectParentStorageLocation::class, [
            'storageLocation' => $storageLocation,
            'selectedStorageLocation' => $parentStorageLocation,
        ]);

        // Check selectedPath is initialized correctly.
        $component->assertSet('selectedPath', fn ($path) => count($path) === 2
            && $path[0]->id === $grantParentStorageLocation->id
            && $path[1]->id === $parentStorageLocation->id);

        // Set level 2 to a sibling.
        $component->call('selectStorageLocation', 2, $siblingStorageLocation->id)
            ->assertSet('selectedPath', fn ($path) => count($path) === 3
                && $path[0]->id === $grantParentStorageLocation->id
                && $path[1]->id === $parentStorageLocation->id
                && $path[2]->id === $siblingStorageLocation->id)
            ->assertSet('selectedStorageLocation.id', $siblingStorageLocation->id);

        // Set level 1 to a sibling of the parent.
        $component->call('selectStorageLocation', 1, $siblingOfParentStorageLocation->id)
            ->assertSet('selectedPath', fn ($path) => count($path) === 2
                && $path[0]->id === $grantParentStorageLocation->id
                && $path[1]->id === $siblingOfParentStorageLocation->id)
            ->assertSet('selectedStorageLocation.id', $siblingOfParentStorageLocation->id);

        // Set level 1 to null (resets the selection for the level).
        $component->call('selectStorageLocation', 1, null)
            ->assertSet('selectedPath', fn ($path) => count($path) === 1
                && $path[0]->id === $grantParentStorageLocation->id)
            ->assertSet('selectedStorageLocation.id', $grantParentStorageLocation->id);

        // Set level 0 to another root storage location.
        $component->call('selectStorageLocation', 0, $anotherRootStorageLocation->id)
            ->assertSet('selectedPath', fn ($path) => count($path) === 1
                && $path[0]->id === $anotherRootStorageLocation->id)
            ->assertSet('selectedStorageLocation.id', $anotherRootStorageLocation->id);

        // Set level 0 to null (resets the selection).
        $component->call('selectStorageLocation', 0, null)
            ->assertSet('selectedPath', fn ($path) => count($path) === 0)
            ->assertSet('selectedStorageLocation.id', null);
    }
}
