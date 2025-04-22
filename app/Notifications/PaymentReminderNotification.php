<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Booking $booking)
    {
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return $this->booking->prepareMailMessage()
            ->subject(
                /** @phpstan-ignore-next-line binaryOp.invalid */
                config('app.name')
                . ': '
                . __('Pending payment for booking no. :id', [
                    'id' => $this->booking->id,
                ])
            )
            ->line(__('we have received your booking for :name dated :date, but have not yet been able to confirm receipt of payment.', [
                'name' => $this->booking->name,
                /** @phpstan-ignore-next-line argument.type */
                'date' => formatDate($this->booking->booked_at),
            ]))
            ->line(__('Please transfer :price to the following bank account as soon as possible:', [
                /** @phpstan-ignore-next-line argument.type */
                'price' => formatDecimal($this->booking->price) . ' €',
            ]))
            ->lines($this->booking->bookingOption->event->organization->bank_account_lines);
    }

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }
}
