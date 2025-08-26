<?php

namespace App\Exports;

use App\Exports\Traits\ExportsToExcel;
use App\Models\Material;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class MaterialsExportSpreadsheet extends Spreadsheet
{
    use ExportsToExcel;

    public function __construct(
        /** @var Collection<int, Material> */
        private readonly Collection $materials,
    ) {
        parent::__construct();

        $this->setMetaData(__('Materials'));

        /** @var \Illuminate\Support\Collection<int, array{Material, ?StorageLocation}> $materialsWithStorageData */
        $materialsWithStorageData = \Illuminate\Support\Collection::empty();
        foreach ($this->materials as $material) {
            if ($material->storageLocations->isEmpty()) {
                $materialsWithStorageData->add([$material, null]);
                continue;
            }

            foreach ($material->storageLocations as $storageLocation) {
                $materialsWithStorageData->add([$material, $storageLocation]);
            }
        }

        self::fillSheetFromCollection(
            $this->getActiveSheet(),
            __('Materials'),
            $materialsWithStorageData,
            $this->getHeaderColumns(),
            function (array $data) {
                [$material, $storageLocation] = $data;
                return $this->getColumnsForRow($material, $storageLocation);
            }
        );
    }

    /**
     * @return string[]
     */
    private function getHeaderColumns(): array
    {
        return [
            __('Name'),
            __('Description'),
            __('Organization'),
            __('Storage location'),
            __('Status'),
            __('Stock'),
            __('Remarks'),
        ];
    }

    /**
     * @return array<int, float|int|string|null>
     */
    private function getColumnsForRow(Material $material, ?StorageLocation $storageLocation): array
    {
        return [
            $material->name,
            $material->description,
            $material->organization->name,
            $storageLocation->name ?? null,
            $storageLocation?->pivot->material_status->getTranslatedName(),
            $storageLocation?->pivot->stock ?? null,
            $storageLocation?->pivot->remarks ?? null,
        ];
    }
}
