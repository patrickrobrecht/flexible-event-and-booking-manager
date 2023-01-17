<?php

namespace App\Listeners;

use App\Events\BookingCompleted;
use App\Notifications\BookingConfirmation;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendBookingConfirmation implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param BookingCompleted $event
     *
     * @return void
     */
    public function handle(BookingCompleted $event)
    {
        $notification = Notification::route('mail', $event->booking->email);
        if (isset($event->booking->bookedByUser) && $event->booking->bookedByUser->email !== $event->booking->email) {
            $notification->route('mail', $event->booking->bookedByUser->email);
        }
        $notification->notify(
            new BookingConfirmation($event->booking)
        );
        Log::info(
            sprintf(
                'Sent mail notification for booking %s to %s and %s',
                $event->booking->id,
                $event->booking->email,
                $event->booking->bookedByUser->email ?? '-'
            )
        );
    }
}
