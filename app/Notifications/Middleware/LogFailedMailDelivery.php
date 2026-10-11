<?php

namespace App\Notifications\Middleware;

use Closure;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Queue\Jobs\SyncJob;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Logs failed attempts to send a notification (including whether it will be retried) and rethrows the exception.
 */
class LogFailedMailDelivery
{
    public function handle(SendQueuedNotifications $job, Closure $next): mixed
    {
        try {
            return $next($job);
        } catch (Throwable $exception) {
            $attempt = $job->attempts();
            // The sync queue does not retry failed jobs.
            $maxAttempts = $job->job instanceof SyncJob ? 1 : $job->tries;
            $context = [
                'notification' => $job->notification::class,
                'notification_id' => $job->notification->id,
                'exception' => $exception->getMessage(),
            ];
            $recipients = implode(', ', $this->getRecipients($job));

            if ($attempt < $maxAttempts) {
                $delayInSeconds = $this->getDelayInSeconds($job, $attempt);
                Log::warning(
                    sprintf(
                        'Sending mail to %s failed (attempt %d of %d), retry in %d minutes',
                        $recipients,
                        $attempt,
                        $maxAttempts,
                        (int) ceil($delayInSeconds / 60)
                    ),
                    $context
                );
            } else {
                Log::error(
                    sprintf(
                        'Sending mail to %s failed finally (attempt %d of %d)',
                        $recipients,
                        $attempt,
                        $maxAttempts
                    ),
                    $context
                );
            }

            throw $exception;
        }
    }

    /**
     * Get the delay before the next attempt (the last backoff value is used for all further attempts).
     */
    private function getDelayInSeconds(SendQueuedNotifications $job, int $attempt): int
    {
        $backoff = $job->backoff();
        $delays = array_values(is_array($backoff) ? $backoff : [$backoff]);
        $delay = $delays[min($attempt, count($delays)) - 1] ?? 0;

        return is_numeric($delay) ? (int) $delay : 0;
    }

    /**
     * @return string[]
     */
    private function getRecipients(SendQueuedNotifications $job): array
    {
        $recipients = [];
        foreach ($job->notifiables as $notifiable) {
            if (!is_object($notifiable) || !method_exists($notifiable, 'routeNotificationFor')) {
                continue;
            }

            $route = $notifiable->routeNotificationFor('mail', $job->notification);
            foreach ((array) $route as $key => $value) {
                // Routes may be given as email => name.
                $recipients[] = is_string($key) ? $key : (string) $value;
            }
        }

        return $recipients;
    }
}
