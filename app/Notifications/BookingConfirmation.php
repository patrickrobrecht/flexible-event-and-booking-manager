<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmation extends Notification
{
    use Queueable;

    public function __construct(private readonly Booking $booking)
    {
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        $mail = $this->booking->prepareMailMessage()
            ->subject(
                config('app.name')
                . ': '
                . __('Booking no. :id', [
                    'id' => $this->booking->id,
                ])
            )
            ->line(__('we received your booking:'))
            ->line($this->booking->name);

        if (isset($this->booking->street)) {
            $mail->line($this->booking->street . ' ' . ($this->booking->house_number ?? ''));
        }
        if (isset($this->booking->postal_code) || isset($this->booking->city)) {
            $mail->line(($this->booking->postal_code ?? '') . ' ' . ($this->booking->city ?? ''));
        }

        if (isset($this->booking->price) && $this->booking->price > 0) {
            $mail->line(__('Please transfer :price to the following bank account by :date:', [
                'price' => formatDecimal($this->booking->price) . ' €',
                'date' => formatDate($this->booking->payment_deadline),
            ]));
            $mail->lines($this->booking->bookingOption->event->organization->bank_account_lines);
        }

        if (isset($this->booking->bookingOption->confirmation_text)) {
            $mail->line($this->booking->bookingOption->confirmation_text);
        }

        return $mail;
    }

    /**
     * @return array<int, string>
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }
}
