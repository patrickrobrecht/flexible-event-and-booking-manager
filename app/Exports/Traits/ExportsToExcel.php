<?php

namespace App\Exports\Traits;

use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @mixin Spreadsheet
 */
trait ExportsToExcel
{
    public function setMetaData(string $title): void
    {
        $this->getProperties()
            ->setCreator(config('app.name') . ' - ' . config('app.owner'))
            ->setTitle($title);
    }

    public static function fillSheetFromCollection(
        Worksheet $worksheet,
        string $title,
        Collection $collection,
        array $headerColumns,
        \Closure $rowProvider
    ): Worksheet {
        $worksheet->setTitle(substr(str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $title), 0, 31));
        $worksheet->fromArray([
            [$title],
            [],
            $headerColumns,
            ...$collection->map($rowProvider),
        ]);

        $columnCount = count($headerColumns);
        self::formatHeadline($worksheet, $columnCount);
        $worksheet->setAutoFilter([1, 3, $columnCount, 3]);
        self::setAutoSizeForColumns($worksheet, $columnCount);

        return $worksheet;
    }

    public static function formatHeadline(Worksheet $worksheet, int $columnCount): void
    {
        $worksheet->mergeCells([1, 1, $columnCount, 1]);
        $worksheet->getCell('A1')->getStyle()->getFont()->setSize(18);
        $worksheet->getCell('A1')->getStyle()->getFont()->setName('Calibri Light');
        $worksheet->getCell('A1')->getStyle()->getFont()->getColor()->setRGB('44546A');
    }

    public static function setAutoSizeForColumns(Worksheet $worksheet, int $columnCount): void
    {
        foreach (range(1, $columnCount) as $column) {
            $worksheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }
    }
}
