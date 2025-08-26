<?php

namespace Tests\Unit\Exports;

use App\Enums\MaterialStatus;
use App\Exports\MaterialsExportSpreadsheet;
use App\Models\Material;
use App\Models\StorageLocation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(MaterialsExportSpreadsheet::class)]
class MaterialsExportSpreadsheetTest extends TestCase
{
    use RefreshDatabase;

    public function testSpreadsheetContainsMaterialWhichDoesNotHaveAnyStorageLocation(): void
    {
        $material = self::createMaterial(0);

        $sheet = $this->getSheet();
        $this->assertHeaderRow($sheet);
        $this->assertMaterialInRow($sheet, $material, 4);
    }

    public function testSpreadsheetContainsRowForEachStorageLocationWithPivotData(): void
    {
        // Create a material with two storage locations to ensure multiple rows are exported
        $material = self::createMaterial(2);

        // Ensure known values for assertions
        foreach ($material->storageLocations as $i => $location) {
            $location->pivot->material_status = MaterialStatus::Checked;
            $location->pivot->stock = 10 + $i;
            $location->pivot->remarks = 'Remark ' . ($i + 1);
            $location->pivot->save();
        }

        $sheet = $this->getSheet();
        $this->assertHeaderRow($sheet);
        $locations = $material->storageLocations->values()->all();
        $this->assertMaterialInRow($sheet, $material, 4);
        $this->assertStorageLocationInRow($sheet, $locations[0], 4);
        $this->assertMaterialInRow($sheet, $material, 5);
        $this->assertStorageLocationInRow($sheet, $locations[1], 5);
    }

    private function getSheet(): Worksheet
    {
        $spreadsheet = new MaterialsExportSpreadsheet(
            Material::query()
                ->with([
                    'organization',
                    'storageLocations',
                ])
                ->get()
        );
        return $spreadsheet->getActiveSheet();
    }

    private function assertHeaderRow(Worksheet $sheet): void
    {
        $this->assertSame(__('Materials'), $sheet->getCell('A1')->getValue());
        $headers = [
            'A3' => __('Name'),
            'B3' => __('Description'),
            'C3' => __('Organization'),
            'D3' => __('Storage location'),
            'E3' => __('Status'),
            'F3' => __('Stock'),
            'G3' => __('Remarks'),
        ];
        foreach ($headers as $cell => $expected) {
            $this->assertSame($expected, $sheet->getCell($cell)->getValue());
        }
    }

    public function assertMaterialInRow(Worksheet $sheet, Material $material, int $row): void
    {
        $this->assertSame($material->name, $sheet->getCell('A' . $row)->getValue());
        $this->assertSame($material->description, $sheet->getCell('B4')->getValue());
        $this->assertSame($material->organization->name, $sheet->getCell('C4')->getValue());
    }

    public function assertStorageLocationInRow(Worksheet $sheet, StorageLocation $location, int $row): void
    {
        $this->assertSame($location->name, $sheet->getCell('D' . $row)->getValue());
        $this->assertSame(__('checked'), $sheet->getCell('E' . $row)->getValue());
        $this->assertSame($row + 6, $sheet->getCell('F' . $row)->getValue());
        $this->assertSame('Remark ' . ($row - 3), $sheet->getCell('G' . $row)->getValue());
    }
}
