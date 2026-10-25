<?php

namespace App\Listeners;

use Illuminate\Mail\Events\MessageSent;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Mime\Address;

/**
 * Logs every e-mail which has actually been sent (i.e. handed over to the mail server successfully).
 */
class LogSentMail
{
    public function handle(MessageSent $event): void
    {
        $recipients = array_map(
            static fn (Address $address) => $address->getAddress(),
            $event->sent->getEnvelope()->getRecipients()
        );

        Log::info(
            sprintf(
                'Sent mail "%s" to %s',
                $event->message->getSubject() ?? '',
                implode(', ', $recipients)
            ),
            [
                'message_id' => $event->sent->getMessageId(),
                'notification' => $event->data['__laravel_notification'] ?? null,
                'notification_id' => $event->data['__laravel_notification_id'] ?? null,
            ]
        );
    }
}
