<?php

namespace App\Notifications\Traits;

use App\Notifications\Middleware\LogFailedMailDelivery;
use Illuminate\Bus\Queueable;

/**
 * Notifications using this trait (and implementing ShouldQueue) are sent asynchronously via the queue.
 * If sending fails (e.g. because the mail server is temporarily unavailable),
 * delivery is retried after 5 minutes, 15 minutes, and 1 hour.
 * Each failed attempt is logged.
 */
trait RetriesMailDelivery
{
    use Queueable;

    /**
     * The number of times the notification may be attempted (initial attempt + 3 retries).
     */
    public int $tries = 4;

    /**
     * @return int[] seconds to wait before retrying
     */
    public function backoff(): array
    {
        return [
            5 * 60,
            15 * 60,
            60 * 60,
        ];
    }

    /**
     * @return object[]
     */
    public function middleware(mixed $notifiable, string $channel): array
    {
        return [
            new LogFailedMailDelivery(),
        ];
    }
}
