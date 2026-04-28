<?php

namespace Tests\Unit\Exports;

use App\Exports\StorageLocationsExportSpreadsheet;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(StorageLocation::class)]
#[CoversClass(StorageLocationsExportSpreadsheet::class)]
class StorageLocationsExportSpreadsheetTest extends TestCase
{
    public function testSpreadsheetContainsStorageLocationData(): void
    {
        $s1 = self::createStorageLocation(attributes: ['name' => 'S1']);
        $s11 = self::createStorageLocation($s1, attributes: ['name' => 'S1.1']);
        $s111 = self::createStorageLocation($s11, materialsCount: 2, attributes: ['name' => 'S1.1.1']);
        $s112 = self::createStorageLocation($s11, materialsCount: 3, attributes: ['name' => 'S1.1.2']);
        $s12 = self::createStorageLocation($s1, materialsCount: 4, attributes: ['name' => 'S1.2']);
        $s2 = self::createStorageLocation(materialsCount: 5, attributes: ['name' => 'S2']);

        $spreadsheet = new StorageLocationsExportSpreadsheet(Collection::make([$s1, $s2]));
        $sheet = $spreadsheet->getActiveSheet();
        $this->assertHeaderRow($sheet);

        $materialNames = static fn (StorageLocation $s) => $s->materials->sortBy('name')->pluck('name')->implode(' ● ');
        $this->assertStorageLocationInRow($sheet, $s1, 4, null);
        $this->assertStorageLocationInRow($sheet, $s11, 5, null);
        $this->assertStorageLocationInRow($sheet, $s111, 6, $materialNames($s111));
        $this->assertStorageLocationInRow($sheet, $s112, 7, $materialNames($s112));
        $this->assertStorageLocationInRow($sheet, $s12, 8, $materialNames($s12));
        $this->assertStorageLocationInRow($sheet, $s2, 9, $materialNames($s2));
    }

    private function assertHeaderRow(Worksheet $sheet): void
    {
        self::assertSame(__('Storage locations'), $sheet->getCell('A1')->getValue());
        $headers = [
            'A3' => __('Parent storage location'),
            'B3' => __('Name'),
            'C3' => __('Description'),
            'D3' => __('Packaging instructions'),
            'E3' => __('Materials'),
        ];
        foreach ($headers as $cell => $expected) {
            self::assertSame($expected, $sheet->getCell($cell)->getValue());
        }
    }

    private function assertStorageLocationInRow(Worksheet $sheet, StorageLocation $location, int $row, ?string $expectedMaterials): void
    {
        self::assertSame($location->parentStorageLocation?->name, $sheet->getCell('A' . $row)->getValue());
        self::assertSame($location->name, $sheet->getCell('B' . $row)->getValue());
        self::assertSame($location->description, $sheet->getCell('C' . $row)->getValue());
        self::assertSame($location->packaging_instructions, $sheet->getCell('D' . $row)->getValue());
        self::assertSame($expectedMaterials, $sheet->getCell('E' . $row)->getValue());
    }
}
