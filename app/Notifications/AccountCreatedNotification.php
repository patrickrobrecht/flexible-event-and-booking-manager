<?php

namespace App\Notifications;

use App\Models\User;
use App\Notifications\Traits\RetriesMailDelivery;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class AccountCreatedNotification extends Notification implements ShouldQueue
{
    use RetriesMailDelivery;

    /**
     * The user who created the account (captured at creation, because there is no logged-in user in the queue worker).
     */
    private readonly ?User $adminUser;

    public function __construct(
        private readonly User $user
    ) {
        /** @var ?User $adminUser */
        $adminUser = Auth::user();
        $this->adminUser = $adminUser;
    }

    public function toMail(): MailMessage
    {
        $adminUser = $this->adminUser;

        $mailMessage = (new MailMessage())
            /** @phpstan-ignore binaryOp.invalid */
            ->subject(config('app.name') . ': ' . __('Account created for you'))
            ->greeting($this->user->greeting)
            /** @phpstan-ignore argument.type */
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

    /**
     * @return string[]
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }
}
