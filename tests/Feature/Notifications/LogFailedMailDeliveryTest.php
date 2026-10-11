<?php

namespace Tests\Feature\Notifications;

use App\Notifications\Middleware\LogFailedMailDelivery;
use App\Notifications\Traits\RetriesMailDelivery;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\SendQueuedNotifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\AbstractTransport;
use Tests\TestCase;

#[CoversClass(LogFailedMailDelivery::class)]
#[CoversTrait(RetriesMailDelivery::class)]
class LogFailedMailDeliveryTest extends TestCase
{
    use RefreshDatabase;

    public function testFailedMailIsLoggedAndRetriedWithDatabaseQueue(): void
    {
        $this->useFailingMailer();
        config(['queue.default' => 'database']);
        Carbon::setTestNow('2026-10-11 12:00:00');
        $user = self::createUser();
        $user->sendEmailVerificationNotification();
        $log = Log::spy();

        $this->artisan('queue:work', ['--once' => true])
            /** @phpstan-ignore method.nonObject */
            ->assertSuccessful();

        $log->shouldHaveReceived('warning')
            ->once()
            ->withArgs(static fn (string $message) => $message === "Sending mail to {$user->email} failed (attempt 1 of 4), retry in 5 minutes");
        $job = DB::table('jobs')->sole();
        self::assertSame(1, (int) $job->attempts);
        self::assertSame(Carbon::now()->addMinutes(5)->getTimestamp(), (int) $job->available_at);
    }

    public function testFailedMailIsLoggedAsFinalFailureWithSyncQueue(): void
    {
        $this->useFailingMailer();
        $user = self::createUser();
        $log = Log::spy();

        try {
            $user->sendEmailVerificationNotification();
            self::fail('Exception expected');
        } catch (TransportException) {
        }

        $log->shouldHaveReceived('error')
            ->once()
            ->withArgs(static fn (string $message, array $context) => $message === "Sending mail to {$user->email} failed finally (attempt 1 of 1)"
                && $context['notification'] === VerifyEmailNotification::class
                && $context['exception'] === 'Connection refused');
        $log->shouldNotHaveReceived('warning');
    }

    #[DataProvider('attempts')]
    public function testFailedAttemptIsLoggedWithRetryInformation(int $attempt, string $level, string $expectedMessage): void
    {
        $user = self::createUser();
        $notification = new VerifyEmailNotification($user);
        $notification->id = 'test-id';
        $job = new SendQueuedNotifications(collect([$user]), $notification, ['mail']);
        $queueJob = self::createStub(Job::class);
        $queueJob->method('attempts')->willReturn($attempt);
        $job->setJob($queueJob);
        $log = Log::spy();

        try {
            (new LogFailedMailDelivery())->handle($job, static fn () => throw new RuntimeException('Timeout'));
            self::fail('Exception expected');
        } catch (RuntimeException $exception) {
            self::assertSame('Timeout', $exception->getMessage());
        }

        $log->shouldHaveReceived($level)
            ->once()
            ->withArgs(static fn (string $message, array $context) => $message === "Sending mail to {$user->email} {$expectedMessage}"
                && $context['notification_id'] === 'test-id');
    }

    /**
     * @return array<string, array{int, string, string}>
     */
    public static function attempts(): array
    {
        return [
            'first attempt' => [1, 'warning', 'failed (attempt 1 of 4), retry in 5 minutes'],
            'second attempt' => [2, 'warning', 'failed (attempt 2 of 4), retry in 15 minutes'],
            'third attempt' => [3, 'warning', 'failed (attempt 3 of 4), retry in 60 minutes'],
            'last attempt' => [4, 'error', 'failed finally (attempt 4 of 4)'],
        ];
    }

    private function useFailingMailer(): void
    {
        Mail::extend('failing', static fn () => new class extends AbstractTransport
        {
            protected function doSend(SentMessage $message): void
            {
                throw new TransportException('Connection refused');
            }

            public function __toString(): string
            {
                return 'failing://';
            }
        });
        config([
            'mail.default' => 'failing',
            'mail.mailers.failing' => ['transport' => 'failing'],
        ]);
    }
}
