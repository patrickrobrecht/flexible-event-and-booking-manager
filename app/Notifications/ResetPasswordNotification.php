<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends ResetPassword
{
    public function __construct(
        private User $user,
        string $token,
    ) {
        parent::__construct($token);
    }

    protected function buildMailMessage($url): MailMessage
    {
        return (new MailMessage())
            /** @phpstan-ignore-next-line binaryOp.invalid */
            ->subject(config('app.name') . ': ' . __('Reset password'))
            ->greeting($this->user->greeting . ',')
            ->line(__('You are receiving this email because we received a password reset request for your account.'))
            ->action(__('Reset password'), $url)
            /** @phpstan-ignore-next-line  argument.type */
            ->line(__('This link will expire in :count minutes.', [
                /** @phpstan-ignore-next-line binaryOp.invalid */
                'count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire'),
            ]))
            ->line(__('If you did not request a password reset, no further action is required.'));
    }
}
