<?php

namespace App\Http\Controllers\Traits;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

trait StreamsExport
{
    public function streamExcelExport(Spreadsheet $spreadsheet, string $fileName): StreamedResponse
    {
        $writer = new Xlsx($spreadsheet);

        return response()
            ->streamDownload(
                static function () use ($writer) {
                    $writer->save('php://output');
                },
                $fileName,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                ]
            );
    }
}
