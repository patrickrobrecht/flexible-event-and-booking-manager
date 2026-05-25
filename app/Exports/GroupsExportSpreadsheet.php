<?php

namespace App\Exports;

use App\Exports\Traits\ExportsToExcel;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Group;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class GroupsExportSpreadsheet extends Spreadsheet
{
    use ExportsToExcel;

    private const int COLUMN_COUNT = 5;

    public function __construct(
        private readonly Event $event,
        private readonly string $sort,
    ) {
        parent::__construct();

        $this->setMetaData($this->event->name);
        $this->event->load([
            'groups.bookings',
        ]);

        $worksheet = $this->getActiveSheet();
        self::setTitle($worksheet, $this->event->name);

        $worksheet->setCellValue('A1', $this->event->name);
        self::formatHeadline($worksheet, self::COLUMN_COUNT);

        $currentReportRow = 3;
        $bookings = $this->prepareBookings();
        $chunks = $this->event->groups->chunk(self::COLUMN_COUNT);
        foreach ($chunks as $chunk) {
            // Header row
            $worksheet->fromArray([$chunk->map(fn (Group $group) => $group->name)->toArray()], startCell: 'A' . $currentReportRow);
            self::formatBold($worksheet, $currentReportRow, self::COLUMN_COUNT);
            $currentReportRow++;

            // Rows for the bookings
            $maxBookingsInChunk = $chunk->max(fn (Group $group) => count($bookings[$group->id]));
            for ($i = 0; $i < $maxBookingsInChunk; $i++) {
                $col = 1;
                foreach ($chunk as $group) {
                    $name = $bookings[$group->id][$i] ?? null;
                    if ($name !== null) {
                        $worksheet->getCell([$col, $currentReportRow])->setValue($name);
                    }
                    $col++;
                }
                $currentReportRow++;
            }

            // Empty row.
            $currentReportRow++;
        }

        self::setPageLayout($worksheet);
        self::setColumnWidths($worksheet, [
            'A' => 5.2,
            'B' => 5.2,
            'C' => 5.2,
            'D' => 5.2,
            'E' => 5.2,
        ]);
    }

    /**
     * @return array<int, string[]>
     */
    private function prepareBookings(): array
    {
        $parentEvent = $this->event->parentEvent;
        $bookings = [];
        foreach ($this->event->groups as $group) {
            /** @var string[] $groupMembers */
            $groupMembers = Booking::sort($group->bookings, $this->sort)
                ->map(
                    function (Booking $booking) use ($group, $parentEvent) {
                        $name = $booking->first_name . ' ' . $booking->last_name;

                        if (isset($parentEvent)) {
                            $group = $booking->getGroup($parentEvent);
                            if (isset($group)) {
                                $name .= ' (' . $group->name . ')';
                            }
                        }

                        return $name;
                    }
                )
                ->values()
                ->toArray();
            $bookings[$group->id] = $groupMembers;
        }

        return $bookings;
    }
}
