<?php

namespace App\Livewire\StorageLocations;

use App\Livewire\StorageLocations\Traits\SelectsStorageLocation;
use App\Models\StorageLocation;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SelectStorageLocation extends Component
{
    use SelectsStorageLocation;

    #[Locked]
    public ?string $id;

    #[Locked]
    public ?string $name;

    /**
     * @param string $id
     * @param string $name
     * @param ?StorageLocation $selectedStorageLocation
     */
    public function mount($id, $name, $selectedStorageLocation): void
    {
        $this->id = $id;
        $this->name = $name;

        $this->selectedStorageLocation = $selectedStorageLocation;
        if (isset($selectedStorageLocation)) {
            $this->selectedPath = $selectedStorageLocation->getAncestorsAndSelf();
        }

        $this->initRootStorageLocations();
    }

    public function selectStorageLocation(int $index, ?string $storageLocationId): void
    {
        if ($storageLocationId === null) {
            // null is forbidden!
            return;
        }

        $this->updateSelectedPathAndStorageLocation($index, $storageLocationId);
    }

    public function render(): View
    {
        return view('livewire.storage-locations.select-storage-location');
    }
}
