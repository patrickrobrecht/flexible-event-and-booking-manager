<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail
{
    public function __construct(
        private User $user
    ) {
    }

    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage())
            ->subject(config('app.name') . ': ' . __('Verify e-mail address'))
            ->greeting($this->user->greeting . ',')
            ->line(__('Please click the button below to verify your e-mail address.'))
            ->action(__('Verify e-mail address'), $url)
            ->line(__('If you did not create an account, no further action is required.'));
    }
}
