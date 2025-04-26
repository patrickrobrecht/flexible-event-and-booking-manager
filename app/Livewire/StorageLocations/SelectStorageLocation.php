<?php

namespace App\Livewire\StorageLocations;

use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Locked;
use Livewire\Component;

class SelectStorageLocation extends Component
{
    /** @var Collection<int, StorageLocation>|null */
    #[Locked]
    public ?Collection $allowedRootStorageLocations;

    /**
     * @var array<int, StorageLocation>
     */
    public array $selectedPath = [];
    public ?StorageLocation $selectedStorageLocation = null;

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

        $this->allowedRootStorageLocations = StorageLocation::query()
            ->whereNull('parent_storage_location_id')
            ->orderBy('name')
            ->get();
    }

    public function selectStorageLocation(int $index, ?string $storageLocationId): void
    {
        if ($storageLocationId) {
            $newStorageLocation = StorageLocation::query()->find((int) $storageLocationId);

            if ($newStorageLocation) {
                $this->selectedPath[$index] = $newStorageLocation;
                array_splice($this->selectedPath, $index + 1);
            }
        } else {
            array_splice($this->selectedPath, $index);
        }

        $selectedStorageLocation = end($this->selectedPath);
        $this->selectedStorageLocation = $selectedStorageLocation === false
            ? null
            : $selectedStorageLocation;
    }

    public function render(): View
    {
        return view('livewire.storage-locations.select-storage-location');
    }
}
