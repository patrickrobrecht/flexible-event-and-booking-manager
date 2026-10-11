<?php

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Traits\RetriesMailDelivery;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class VerifyEmailNotification extends VerifyEmail implements ShouldQueue
{
    use RetriesMailDelivery;

    public function __construct(
        private User $user
    ) {
    }

    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage())
            /** @phpstan-ignore binaryOp.invalid */
            ->subject(config('app.name') . ': ' . __('Verify e-mail address'))
            ->greeting($this->user->greeting)
            ->line(__('Please click the button below to verify your e-mail address.'))
            ->action(__('Verify e-mail address'), $url)
            ->line(__('If you did not create an account, no further action is required.'));
    }
}
