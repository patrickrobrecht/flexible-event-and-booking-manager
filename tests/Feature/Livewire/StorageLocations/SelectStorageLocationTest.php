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
        Livewire::test(SelectStorageLocation::class, ['id' => 'storage_location_id', 'name' => 'storage_location_id', 'selectedStorageLocation' => null])
            ->assertSeeHtml('Lagerplatz auswählen</option>')
            ->assertOk();
    }

    public function testSelectStorageLocationRemovesPathElementsWhenIdIsNull(): void
    {
        $storageLocation1 = self::createStorageLocation();
        $storageLocation1a = self::createStorageLocation($storageLocation1);
        $storageLocation1a1 = self::createStorageLocation($storageLocation1a);
        $storageLocation1a2 = self::createStorageLocation($storageLocation1a);
        $storageLocation1b = self::createStorageLocation($storageLocation1);

        $storageLocation2 = self::createStorageLocation();

        $component = Livewire::test(SelectStorageLocation::class, [
            'id' => 'storage_location_id',
            'name' => 'storage_location_id',
            'selectedStorageLocation' => $storageLocation1a1,
        ]);

        // Check selectedPath is initialized correctly.
        $component->assertSet('selectedPath', fn ($path) => count($path) === 3
            && $path[0]->id === $storageLocation1->id
            && $path[1]->id === $storageLocation1a->id
            && $path[2]->id === $storageLocation1a1->id);

        // Set level 2 to a sibling of the initial selection.
        $component->call('selectStorageLocation', 2, $storageLocation1a2->id)
            ->assertSet('selectedPath', fn ($path) => count($path) === 3
                && $path[0]->id === $storageLocation1->id
                && $path[1]->id === $storageLocation1a->id
                && $path[2]->id === $storageLocation1a2->id)
            ->assertSet('selectedStorageLocation.id', $storageLocation1a2->id);

        // Set level 1 to another.
        $component->call('selectStorageLocation', 1, $storageLocation1b->id)
            ->assertSet('selectedPath', fn ($path) => count($path) === 2
                && $path[0]->id === $storageLocation1->id
                && $path[1]->id === $storageLocation1b->id)
            ->assertSet('selectedStorageLocation.id', $storageLocation1b->id);

        // Set level 1 to null (changes nothing).
        $component->call('selectStorageLocation', 1, null)
            ->assertSet('selectedStorageLocation.id', $storageLocation1b->id);

        // Set level 0 to another root storage location.
        $component->call('selectStorageLocation', 0, $storageLocation2->id)
            ->assertSet('selectedPath', fn ($path) => count($path) === 1
                && $path[0]->id === $storageLocation2->id)
            ->assertSet('selectedStorageLocation.id', $storageLocation2->id);

        // Set level 0 to null (changes nothing).
        $component->call('selectStorageLocation', 0, null)
            ->assertSet('selectedStorageLocation.id', $storageLocation2->id);
    }
}
