<?php

namespace App\Livewire\StorageLocations;

use App\Livewire\StorageLocations\Traits\SelectsStorageLocation;
use App\Models\StorageLocation;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SelectParentStorageLocation extends Component
{
    use SelectsStorageLocation;

    #[Locked]
    public ?StorageLocation $storageLocation;

    /**
     * @param StorageLocation $storageLocation
     * @param ?StorageLocation $selectedStorageLocation
     */
    public function mount($storageLocation, $selectedStorageLocation): void
    {
        $this->storageLocation = $storageLocation;

        $this->selectedStorageLocation = $selectedStorageLocation;
        if (isset($selectedStorageLocation)) {
            $this->selectedPath = $selectedStorageLocation->getAncestorsAndSelf();
        }

        $this->initRootStorageLocations();
    }

    public function selectStorageLocation(int $index, ?string $storageLocationId): void
    {
        $this->updateSelectedPathAndStorageLocation($index, $storageLocationId, false);
    }

    public function render(): View
    {
        return view('livewire.storage-locations.select-parent-storage-location');
    }
}
