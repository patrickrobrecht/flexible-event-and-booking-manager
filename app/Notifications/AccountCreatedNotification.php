<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class AccountCreatedNotification extends Notification
{
    public function __construct(
        private readonly User $user
    ) {
    }

    public function toMail(): MailMessage
    {
        $adminUser = Auth::user();

        $mailMessage = (new MailMessage())
            ->subject(config('app.name') . ': ' . __('Account created for you'))
            ->greeting(__($this->user->greeting . ','))
            ->line(__(':admin_name has created an account for :app_name for you.', [
                'admin_name' => $adminUser->name ?? __('someone'),
                'app_name' => config('app.name'),
            ]))
            ->action(__('Reset password'), route('password.email'))
            ->line(__('Use the link above to create a password that you can use to log in.'));

        if (isset($adminUser)) {
            $mailMessage->replyTo($adminUser->email, $adminUser->name);
        }

        return $mailMessage;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }
}