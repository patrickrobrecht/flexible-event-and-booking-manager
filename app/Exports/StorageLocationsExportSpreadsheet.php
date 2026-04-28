<?php

namespace App\Exports;

use App\Exports\Traits\ExportsToExcel;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class StorageLocationsExportSpreadsheet extends Spreadsheet
{
    use ExportsToExcel;

    public function __construct(
        /** @var Collection<int, StorageLocation> */
        private readonly Collection $storageLocations,
    ) {
        parent::__construct();

        $this->setMetaData(__('Storage locations'));

        $worksheet = $this->getActiveSheet();
        self::fillSheetFromCollection(
            $this->getActiveSheet(),
            __('Storage locations'),
            StorageLocation::flatten($this->storageLocations),
            $this->getHeaderColumns(),
            fn (StorageLocation $storageLocation) => $this->getColumnsForRow($storageLocation)
        );
        self::setColumnWidths($worksheet, [
            'A' => 5,
            'B' => 5,
            'C' => 4,
            'D' => 4,
            'E' => 8,
        ]);
    }

    /**
     * @return string[]
     */
    private function getHeaderColumns(): array
    {
        return [
            __('Parent storage location'),
            __('Name'),
            __('Description'),
            __('Packaging instructions'),
            __('Materials'),
        ];
    }

    /**
     * @return array<int, float|int|string|null>
     */
    private function getColumnsForRow(StorageLocation $storageLocation): array
    {
        return [
            $storageLocation->parentStorageLocation?->name,
            $storageLocation->name,
            $storageLocation->description,
            $storageLocation->packaging_instructions,
            $storageLocation->materials
                ->pluck('name')
                ->sort()
                ->implode(' ● '),
        ];
    }
}
