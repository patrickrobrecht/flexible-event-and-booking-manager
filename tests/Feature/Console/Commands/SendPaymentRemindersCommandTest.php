<?php

namespace Tests\Feature\Console\Commands;

use App\Console\Commands\SendPaymentRemindersCommand;
use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Notifications\PaymentReminderNotification;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Booking::class)]
#[CoversClass(PaymentReminderNotification::class)]
#[CoversClass(SendPaymentRemindersCommand::class)]
class SendPaymentRemindersCommandTest extends TestCase
{
    use GeneratesTestData;
    use RefreshDatabase;

    public function testCommandRunsWithNoOverdueBookings(): void
    {
        /** @phpstan-ignore method.nonObject */
        $this->artisan('app:send-payment-reminders')
            ->expectsOutput('There are no booking options with unpaid bookings to check.')
            ->assertSuccessful();
    }

    public function testCommandSendsPaymentReminders(): void
    {
        Notification::fake();
        Log::shouldReceive('info')->once();

        $booking = $this->fakeUnpaidBooking();

        /** @phpstan-ignore method.nonObject */
        $this->artisan('app:send-payment-reminders')
            ->expectsOutputToContain("Sent reminder to {$booking->email} for booking {$booking->id}.")
            ->assertSuccessful();
        Notification::assertSentTo(new AnonymousNotifiable(), PaymentReminderNotification::class, static function ($notification) use ($booking) {
            /** @phpstan-ignore-next-line argument.type */
            return str_contains($notification->toMail($booking->bookedByUser)->render(), $booking->bookedByUser->greeting);
        });
        Notification::assertCount(1);
    }

    public function testCommandOutputsLogDataDuringDryRun(): void
    {
        Notification::fake();
        Log::shouldReceive('info')->once();

        $booking = $this->fakeUnpaidBooking();

        /** @phpstan-ignore method.nonObject */
        $this->artisan('app:send-payment-reminders --dry-run')
            ->expectsOutputToContain("Reminder to {$booking->email} for booking {$booking->id} not sent because it's a dry run.")
            ->assertSuccessful();
        Notification::assertNothingSent();
    }

    public function testCommandDoesNotSendPaymentRemindersForWaitingListBookings(): void
    {
        Notification::fake();

        // One overdue booking confirmed -> should get reminder
        $confirmedBooking = $this->fakeUnpaidBooking();

        // One overdue booking on waiting list -> should NOT get reminder
        $waitingListBooking = $this->fakeUnpaidBooking();
        $waitingListBooking->update(['status' => BookingStatus::Waiting]);

        /** @phpstan-ignore method.nonObject */
        $this->artisan('app:send-payment-reminders')
            ->expectsOutputToContain("Sent reminder to {$confirmedBooking->email} for booking {$confirmedBooking->id}.")
            ->doesntExpectOutputToContain("Sent reminder to {$waitingListBooking->email} for booking {$waitingListBooking->id}.")
            ->assertSuccessful();

        Notification::assertSentToTimes(new AnonymousNotifiable(), PaymentReminderNotification::class, 1);
        Notification::assertNotSentTo(
            new AnonymousNotifiable(),
            PaymentReminderNotification::class,
            static fn (PaymentReminderNotification $notification) => $notification->booking->id === $waitingListBooking->id
        );
    }

    private function fakeUnpaidBooking(): Booking
    {
        $bookingOption = self::createBookingOptionForEvent(attributes: [
            'price' => 5,
            'payment_due_days' => 10,
        ]);
        return self::createBooking($bookingOption, [
            'paid_at' => null,
            'deleted_at' => null,
            /** @phpstan-ignore-next-line method.nonObject */
            'booked_at' => Carbon::today()->subWeekDays(11),
        ]);
    }
}
