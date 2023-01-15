<?php

namespace App\Notifications;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingConfirmation extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(private Booking $booking)
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        $mail = (new MailMessage())
            ->subject(config('app.name') . ': ' . __('Booking'))
            ->line(__('we received your booking:'))
            ->line($this->booking->first_name . ' ' . $this->booking->last_name);

        if (isset($this->booking->street)) {
            $mail->line($this->booking->street . ' ' . ($this->booking->house_number ?? ''));
        }
        if (isset($this->booking->postal_code) || isset($this->booking->city)) {
            $mail->line(($this->booking->postal_code ?? '') . ' ' . ($this->booking->city ?? ''));
        }

        if (isset($this->booking->price) && $this->booking->price > 0) {
            $mail->line(__('Please transfer :price to the following bank account:', [
                'price' => formatDecimal($this->booking->price) . ' €',
            ]));
            $mail->lines([
                'IBAN: ' . config('app.bank_account.iban'),
                __('Bank') . ': ' . config('app.bank_account.bank_name'),
                __('Account holder') . ': ' . config('app.bank_account.holder')
            ]);
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->booking->toArray();
    }
}
