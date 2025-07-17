<?php

namespace App\Exports;

use App\Exports\Traits\ExportsToExcel;
use App\Models\Material;
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

        self::fillSheetFromCollection(
            $this->getActiveSheet(),
            __('Materials'),
            $materials,
            $this->getHeaderColumns(),
            fn (Material $material) => $this->getColumnsForRow($material)
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
        ];
    }

    /**
     * @return array<int, float|int|string|null>
     */
    private function getColumnsForRow(Material $material): array
    {
        return [
            $material->name,
            $material->description,
        ];
    }
}
