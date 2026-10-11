<?php

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Traits\RetriesMailDelivery;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword implements ShouldQueue
{
    use RetriesMailDelivery;

    public function __construct(
        private User $user,
        string $token,
    ) {
        parent::__construct($token);
    }

    /**
     * @return int[] seconds to wait before retrying
     */
    public function backoff(): array
    {
        return [
            5 * 60,
            15 * 60,
            // The link expires after an hour.
            30 * 60,
        ];
    }

    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage())
            /** @phpstan-ignore binaryOp.invalid */
            ->subject(config('app.name') . ': ' . __('Reset password'))
            ->greeting($this->user->greeting)
            ->line(__('You are receiving this email because we received a password reset request for your account.'))
            ->action(__('Reset password'), $url)
            /** @phpstan-ignore  argument.type */
            ->line(__('This link will expire in :count minutes.', [
                /** @phpstan-ignore binaryOp.invalid */
                'count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'),
            ]))
            ->line(__('If you did not request a password reset, no further action is required.'));
    }
}
