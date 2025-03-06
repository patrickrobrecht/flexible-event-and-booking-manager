<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\BookingOption;
use App\Notifications\PaymentReminderNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendPaymentReminders extends Command
{
    protected $signature = 'app:send-payment-reminders
        {--dry-run : If set the reminders are only listed, but not actually sent.}';
    protected $description = 'Send payment reminders for overdue bookings';

    private bool $isDryRun = false;
    private string $messageBuffer = '';

    public function handle(): int
    {
        /** @var Collection<BookingOption> $bookingOptions */
        $bookingOptions = BookingOption::query()
            ->whereNotNull('payment_due_days')
            ->whereHas(
                'bookings',
                fn (Builder $bookings) => $bookings
                    ->whereNotNull('price')
                    ->whereNull('paid_at')
                    ->whereNull('deleted_at')
            )
            ->with([
                'event',
            ])
            ->orderBy('name')
            ->get();

        if ($bookingOptions->isEmpty()) {
            $this->info('There are no booking options with overdue bookings.');
            return self::SUCCESS;
        }

        $this->isDryRun = $this->option('dry-run');

        $bookingOptions = $bookingOptions->sortBy([
            'events.name',
            'name',
        ]);
        foreach ($bookingOptions as $bookingOption) {
            $this->components->task(
                $bookingOption->event->name . ', ' . $bookingOption->name,
                fn () => $this->processBookingsForOption($bookingOption)
            );
        }
        $this->info($this->messageBuffer);

        return self::SUCCESS;
    }

    private function processBookingsForOption(BookingOption $bookingOption): bool
    {
        // Calculate which bookings have reached the due date already.
        $bookedBefore = Carbon::today()->endOfDay()->subWeekdays($bookingOption->payment_due_days);

        /** @var Collection<Booking> $bookingsReadyForReminder */
        $bookingsReadyForReminder = $bookingOption->bookings()
            ->whereNotNull('price')
            ->whereNull('paid_at')
            ->where('booked_at', '<=', $bookedBefore)
            ->orderBy('booked_at')
            ->get();
        if ($bookingsReadyForReminder->isEmpty()) {
            $this->info('No reminders to send.');
            return true;
        }

        $this->output->progressStart($bookingsReadyForReminder->count());

        foreach ($bookingsReadyForReminder as $booking) {
            if ($this->isDryRun) {
                $message = "Reminder to {$booking->email} for booking {$booking->id} not sent because it's a dry run.";
            } else {
                Notification::route('mail', $booking->email)
                    ->notify(new PaymentReminderNotification($booking));
                $message = "Sent reminder to {$booking->email} for booking {$booking->id}.";
            }
            Log::info($message);
            $this->messageBuffer .= $message . PHP_EOL;
            $this->output->progressAdvance();
        }

        $this->output->progressFinish();
        return true;
    }
}
