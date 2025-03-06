<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Booking $booking)
    {
    }

    public function toMail($notifiable)
    {
        return $this->booking->prepareMailMessage()
            ->subject(
                config('app.name')
                . ': '
                . __('Pending payment for booking no. :id', [
                    'id' => $this->booking->id,
                ])
            )
            ->line(__('we have received your booking for :name dated :date, but have not yet been able to confirm receipt of payment.', [
                'name' => $this->booking->name,
                'date' => formatDate($this->booking->booked_at),
            ]))
            ->line(__('Please transfer :price to the following bank account as soon as possible:', [
                'price' => formatDecimal($this->booking->price) . ' €',
            ]))
            ->lines($this->booking->bookingOption->event->organization->bank_account_lines);
    }

    public function via($notifiable)
    {
        return ['mail'];
    }
}
