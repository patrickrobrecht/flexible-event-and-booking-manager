<?php

namespace App\Listeners;

use App\Events\BookingCompleted;
use App\Notifications\BookingConfirmation;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Queues the {@see BookingConfirmation} which is then sent asynchronously (with retries in case of errors).
 */
class SendBookingConfirmation
{
    /**
     * Handle the event.
     */
    public function handle(BookingCompleted $event): void
    {
        $notification = Notification::route('mail', $event->booking->email);
        $notification->notify(
            new BookingConfirmation($event->booking)
        );
        Log::info(
            sprintf(
                'Queued mail notification for booking %s to %s and %s',
                $event->booking->id,
                $event->booking->email,
                $event->booking->bookedByUser->email ?? '-'
            )
        );
    }
}
