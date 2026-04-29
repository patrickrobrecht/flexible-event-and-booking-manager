<?php

namespace App\Exports\Traits;

use Closure;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * @mixin Spreadsheet
 */
trait ExportsToExcel
{
    public function setMetaData(string $title): void
    {
        $this->getProperties()
            /** @phpstan-ignore-next-line binaryOp.invalid */
            ->setCreator(config('app.name') . ' - ' . config('app.owner'))
            ->setTitle($title);
    }

    /**
     * @template TValue
     *
     * @param Collection<int, TValue> $collection
     * @param string[] $headerColumns
     * @param (Closure(TValue): array<int|string, mixed>) $rowProvider
     */
    public static function fillSheetFromCollection(
        Worksheet $worksheet,
        string $title,
        Collection $collection,
        array $headerColumns,
        Closure $rowProvider
    ): void {
        self::setTitle($worksheet, $title);
        self::setPageLayout($worksheet);

        $worksheet->fromArray([
            [$title], // Row 1
            [],
            $headerColumns, // Row 3
            ...$collection->map($rowProvider),
        ]);

        $columnCount = count($headerColumns);
        self::formatHeadline($worksheet, $columnCount);
        self::formatHeaderColumns($worksheet, $columnCount);
        self::setTextWrapping($worksheet, $columnCount, $collection->count() + 3);
    }

    public static function formatHeadline(Worksheet $worksheet, int $columnCount): void
    {
        $worksheet->mergeCells([1, 1, $columnCount, 1]);
        $worksheet->getCell('A1')->getStyle()->getFont()->setSize(18);
        $worksheet->getCell('A1')->getStyle()->getFont()->setName('Calibri Light');
        $worksheet->getCell('A1')->getStyle()->getFont()->getColor()->setRGB('44546A');
    }

    public static function formatHeaderColumns(Worksheet $worksheet, int $columnCount): void
    {
        self::formatBold($worksheet, 3, $columnCount);
        $worksheet->setAutoFilter([1, 3, $columnCount, 3]);
    }

    public static function formatBold(Worksheet $worksheet, int $rowIndex, int $columnCount): void
    {
        $worksheet->getStyle([1, $rowIndex, $columnCount, $rowIndex])
            ->getFont()
            ->setBold(true);
    }

    /**
     * @param array<string, float|int> $columns
     */
    public static function setColumnWidths(Worksheet $worksheet, array $columns): void
    {
        foreach ($columns as $column => $width) {
            $worksheet->getColumnDimension($column)->setAutoSize(false);
            $worksheet->getColumnDimension($column)->setWidth($width, 'cm');
        }
    }

    public static function setAutoSizeForColumns(Worksheet $worksheet, int $columnCount): void
    {
        foreach (range(1, $columnCount) as $column) {
            $worksheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }
    }

    public static function setPageLayout(Worksheet $worksheet): void
    {
        $worksheet->getPageSetup()
            ->setPaperSize(PageSetup::PAPERSIZE_A4)
            ->setOrientation(PageSetup::ORIENTATION_LANDSCAPE);
        $worksheet->getPageMargins()
            ->setLeft(0.39) // ~1cm
            ->setRight(0.39);
    }

    public static function setTextWrapping(Worksheet $worksheet, int $columnCount, int $rowCount): void
    {
        $worksheet->getStyle([1, 1, $columnCount, $rowCount])
            ->getAlignment()
            ->setWrapText(true);
    }

    public static function setTitle(Worksheet $worksheet, string $title): void
    {
        $worksheet->setTitle(substr(str_replace(['*', ':', '/', '\\', '?', '[', ']'], '', $title), 0, 31));
    }
}
