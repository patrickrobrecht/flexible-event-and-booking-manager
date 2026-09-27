<?php

namespace Tests\Unit\Exports;

use App\Enums\FormElementType;
use App\Exports\BookingsExportSpreadsheet;
use App\Models\Booking;
use App\Models\FormField;
use App\Models\User;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(BookingsExportSpreadsheet::class)]
class BookingsExportSpreadsheetTest extends TestCase
{
    public function testSpreadsheetExcludesStaticFormFieldsFromHeaderAndDataRows(): void
    {
        $bookingOption = self::createBookingOptionForEvent();
        FormField::factory()
            ->for($bookingOption)
            ->forType(FormElementType::Headline)
            ->create([
                'sort' => 1,
                'name' => 'Section headline',
            ]);
        $textField = FormField::factory()
            ->for($bookingOption)
            ->forType(FormElementType::Text)
            ->create([
                'sort' => 2,
                'name' => 'Comment',
            ]);

        $paidAt = Carbon::create(2026, 1, 2, 10, 30);
        self::assertNotNull($paidAt);
        $booking = Booking::factory()
            ->for($bookingOption->refresh())
            ->for(User::factory(), 'bookedByUser')
            ->create([
                'price' => $bookingOption->price,
                'paid_at' => $paidAt,
            ]);
        $booking->setFieldValue($textField, 'My comment');

        $spreadsheet = new BookingsExportSpreadsheet(
            $bookingOption->event,
            $bookingOption,
            Booking::query()->whereKey($booking->id)->get()
        );
        $sheet = $spreadsheet->getActiveSheet();

        // The 8 fixed columns occupy A-H; the static headline field must be skipped, so the
        // text field is the next (and only) form field column, and no further column follows it.
        self::assertSame('Comment', $sheet->getCell('I3')->getValue());
        self::assertNull($sheet->getCell('J3')->getValue());

        self::assertSame($paidAt->format('d.m.Y H:i'), $sheet->getCell('G4')->getValue());
        self::assertSame('My comment', $sheet->getCell('I4')->getValue());
        self::assertNull($sheet->getCell('J4')->getValue());
    }
}
