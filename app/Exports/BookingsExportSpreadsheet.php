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
            __('Price'),
            __('Paid at'),
        ];

        if (!isset($this->bookingOption->form)) {
            return array_merge($columns, [
                __('First name'),
                __('Last name'),
                __('Phone number'),
                __('E-mail'),
                __('Street'),
                __('House number'),
                __('Postal code'),
                __('City'),
                __('Country'),
            ]);
        }

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
            isset($booking->booked_at)
                ? $booking->booked_at->format('d.m.Y H:i')
                : '',
            isset($booking->bookedByUser)
                ? sprintf('%s %s', $booking->bookedByUser->first_name, $booking->bookedByUser->last_name)
                : __('Guest'),
            $booking->price ?? 0.00,
            $booking->paid_at
                ? $booking->paid_at->format('d.m.Y H:i')
                : '',
        ];

        if (!isset($this->bookingOption->form)) {
            return array_merge($columns, [
                $booking->first_name,
                $booking->last_name,
                $booking->phone ?? null,
                $booking->email,
                $booking->street,
                $booking->house_number,
                $booking->postal_code,
                $booking->city,
                $booking->country,
            ]);
        }

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
