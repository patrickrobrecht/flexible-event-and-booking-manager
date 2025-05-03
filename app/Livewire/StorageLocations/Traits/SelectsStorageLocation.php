<?php

namespace App\Livewire\StorageLocations\Traits;

use App\Models\StorageLocation;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Locked;

trait SelectsStorageLocation
{
    /** @var Collection<int, StorageLocation>|null */
    #[Locked]
    public ?Collection $allowedRootStorageLocations;

    /**
     * @var array<int, StorageLocation>
     */
    #[Locked]
    public array $selectedPath = [];

    #[Locked]
    public ?StorageLocation $selectedStorageLocation = null;

    protected function initRootStorageLocations(): void
    {
        /** @var Collection<int, StorageLocation> $rootLocationsFromCache */
        $rootLocationsFromCache = Cache::remember(
            'root_storage_locations',
            Carbon::now()->addMinutes(2),
            /** @return Collection<int, StorageLocation> */
            static fn () => StorageLocation::query()
                ->whereNull('parent_storage_location_id')
                ->orderBy('name')
                ->get()
        );

        $this->allowedRootStorageLocations = $rootLocationsFromCache;
    }

    public function updateSelectedPathAndStorageLocation(int $index, ?string $storageLocationId, bool $forceLowestLevel): void
    {
        if ($storageLocationId) {
            $newStorageLocation = StorageLocation::query()->find((int) $storageLocationId);

            if ($newStorageLocation) {
                $this->selectedPath[$index] = $newStorageLocation;
                array_splice($this->selectedPath, $index + 1);

                if ($forceLowestLevel) {
                    while ($newStorageLocation->childStorageLocations->isNotEmpty()) {
                        $firstChildOfNewStorageLocation = $newStorageLocation->childStorageLocations->first();
                        $this->selectedPath[$index + 1] = $firstChildOfNewStorageLocation;
                        $index++;
                        $newStorageLocation = $firstChildOfNewStorageLocation;
                    }
                }
            }
        } else {
            array_splice($this->selectedPath, $index);
        }

        $this->updateSelectedStorageLocation();
    }

    protected function updateSelectedStorageLocation(): void
    {
        $selectedStorageLocation = end($this->selectedPath);
        $this->selectedStorageLocation = $selectedStorageLocation === false
            ? null
            : $selectedStorageLocation;
    }
}
