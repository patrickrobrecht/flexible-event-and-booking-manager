<?php

namespace Tests\Feature\Livewire\StorageLocations;

use App\Livewire\StorageLocations\SelectStorageLocation;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(SelectStorageLocation::class)]
class SelectStorageLocationTest extends TestCase
{
    public function testComponentRendersCorrectly(): void
    {
        Livewire::test(SelectStorageLocation::class, ['storageLocation' => self::createStorageLocation(), 'selectedStorageLocation' => null])
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

        $component = Livewire::test(SelectStorageLocation::class, [
            'storageLocation' => $storageLocation,
            'selectedStorageLocation' => $parentStorageLocation,
        ]);

        // Check selectedPath is initialized correctly.
        $component->assertSet('selectedPath', fn ($path) => count($path) === 2
            && $path[0]->id === $grantParentStorageLocation->id
            && $path[1]->id === $parentStorageLocation->id);

        // Set level 2 sets to a sibling.
        $component->call('selectStorageLocation', 2, $siblingStorageLocation->id)
            ->assertSet('selectedPath', fn ($path) => count($path) === 3
                && $path[0]->id === $grantParentStorageLocation->id
                && $path[1]->id === $parentStorageLocation->id
                && $path[2]->id === $siblingStorageLocation->id)
            ->assertSet('selectedStorageLocation.id', $siblingStorageLocation->id);

        // Set level 1 sets to a sibling of the parent.
        $component->call('selectStorageLocation', 1, $siblingOfParentStorageLocation->id)
            ->assertSet('selectedPath', fn ($path) => count($path) === 2
                && $path[0]->id === $grantParentStorageLocation->id
                && $path[1]->id === $siblingOfParentStorageLocation->id)
            ->assertSet('selectedStorageLocation.id', $siblingOfParentStorageLocation->id);

        // Set level 1 to null.
        $component->call('selectStorageLocation', 1, null)
            ->assertSet('selectedPath', fn ($path) => count($path) === 1
                && $path[0]->id === $grantParentStorageLocation->id)
            ->assertSet('selectedStorageLocation.id', $grantParentStorageLocation->id);

        // Set level 0 to another root storage location.
        $component->call('selectStorageLocation', 0, $anotherRootStorageLocation->id)
            ->assertSet('selectedPath', fn ($path) => count($path) === 1
                && $path[0]->id === $anotherRootStorageLocation->id)
            ->assertSet('selectedStorageLocation.id', $anotherRootStorageLocation->id);

        // Set level 0 to null.
        $component->call('selectStorageLocation', 0, null)
            ->assertSet('selectedPath', fn ($path) => count($path) === 0)
            ->assertSet('selectedStorageLocation.id', null);
    }
}
