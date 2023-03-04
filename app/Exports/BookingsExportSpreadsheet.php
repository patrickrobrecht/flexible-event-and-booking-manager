<?php

namespace App\Exports;

use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Cell\ColumnRange;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class BookingsExportSpreadsheet extends Spreadsheet
{
    /**
     * @param Event $event
     * @param BookingOption $bookingOption
     * @param Collection<Booking> $bookings
     */
    public function __construct(
        private Event $event,
        private BookingOption $bookingOption,
        private Collection $bookings
    ) {
        parent::__construct();

        $sheet = $this->getActiveSheet();
        $sheet->setTitle(substr($this->bookingOption->name, 0, 31));

        $data = $this->getData();
        $sheet->fromArray($data);

        $this->formatColumns($sheet, count($data[0]));
    }

    private function getData(): array
    {
        $data = [
            $this->getHeaderColumns(),
        ];

        foreach ($this->bookings as $booking) {
            $data[] = $this->getColumnsForRow($booking);
        }

        return $data;
    }

    private function getHeaderColumns(): array
    {
        $columns = [
            __('Event'),
            __('Booking option'),
            __('ID'),
            __('Booking date'),
            __('User'),
        ];

        foreach ($this->bookingOption->form->formFieldGroups as $group) {
            foreach ($group->formFields as $field) {
                $columns[] = $field->name;
            }
        }

        return $columns;
    }

    private function getColumnsForRow(Booking $booking): array
    {
        $columns = [
            $this->event->name,
            $this->bookingOption->name,
            $booking->id,
            $booking->booked_at,
            isset($booking->bookedByUser)
                ? sprintf('%s %s', $booking->bookedByUser->first_name, $booking->bookedByUser->last_name)
                : __('Guest'),
        ];

        foreach ($this->bookingOption->form->formFieldGroups as $group) {
            foreach ($group->formFields as $field) {
                $value = $booking->getFieldValue($field);

                if (is_array($value)) {
                    $value = implode(',', $value);
                }

                if ($field->type === 'date') {
                    $value = Carbon::createFromFormat('Y-m-d', $value)->format('d.m.Y');
                }

                $columns[] = $value;
            }
        }

        return $columns;
    }

    private function formatColumns(Worksheet $worksheet, int $columnCount): void
    {
        $worksheet->setAutoFilter(ColumnRange::fromColumnIndexes(1, $columnCount));
        foreach (range(1, $columnCount) as $column) {
            $worksheet->getColumnDimensionByColumn($column)->setAutoSize(true);
        }
    }
}
