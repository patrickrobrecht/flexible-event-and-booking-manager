<?php

namespace App\Exports;

use App\Exports\Traits\ExportsToExcel;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use Illuminate\Database\Eloquent\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class BookingsExportSpreadsheet extends Spreadsheet
{
    use ExportsToExcel;

    /**
     * @param Collection<Booking> $bookings
     */
    public function __construct(
        private Event $event,
        private BookingOption $bookingOption,
        private Collection $bookings
    ) {
        parent::__construct();

        self::fillSheetFromCollection(
            $this->getActiveSheet(),
            $this->bookingOption->name,
            $this->bookings,
            $this->getHeaderColumns(),
            fn (Booking $booking) => $this->getColumnsForRow($booking)
        );
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
            __('Group'),
        ];

        if ($this->bookingOption->formFields->isEmpty()) {
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

        foreach ($this->bookingOption->formFields as $field) {
            if ($field->type->isStatic()) {
                continue;
            }

            $columns[] = $field->name;
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
            $booking->getGroup($this->event)?->name ?? __('none'),
        ];

        if ($this->bookingOption->formFields->isEmpty()) {
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

        foreach ($this->bookingOption->formFields as $field) {
            if ($field->type->isStatic()) {
                continue;
            }

            $columns[] = $booking->getFieldValueAsText($field);
        }

        return $columns;
    }
}
