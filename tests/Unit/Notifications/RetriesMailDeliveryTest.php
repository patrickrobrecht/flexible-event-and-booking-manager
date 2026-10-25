<?php

namespace Tests\Unit\Notifications;

use App\Models\Booking;
use App\Models\User;
use App\Notifications\AccountCreatedNotification;
use App\Notifications\BookingConfirmation;
use App\Notifications\PaymentReminderNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\Traits\RetriesMailDelivery;
use App\Notifications\VerifyEmailNotification;
use Closure;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\SendQueuedNotifications;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversTrait;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(AccountCreatedNotification::class)]
#[CoversClass(BookingConfirmation::class)]
#[CoversClass(PaymentReminderNotification::class)]
#[CoversClass(ResetPasswordNotification::class)]
#[CoversClass(VerifyEmailNotification::class)]
#[CoversTrait(RetriesMailDelivery::class)]
class RetriesMailDeliveryTest extends TestCase
{
    /**
     * @param Closure(): Notification $notificationProvider
     * @param int[] $expectedBackoff
     */
    #[DataProvider('notifications')]
    public function testNotificationIsQueuedWithRetries(Closure $notificationProvider, array $expectedBackoff): void
    {
        $notification = $notificationProvider();
        self::assertInstanceOf(ShouldQueue::class, $notification);

        $job = new SendQueuedNotifications(collect([new User()]), $notification, ['mail']);
        self::assertSame(4, $job->tries);
        self::assertSame($expectedBackoff, $job->backoff());
    }

    /**
     * @return array<string, array{Closure(): Notification, int[]}>
     */
    public static function notifications(): array
    {
        $defaultBackoff = [5 * 60, 15 * 60, 60 * 60];

        return [
            'account created' => [static fn () => new AccountCreatedNotification(new User()), $defaultBackoff],
            'booking confirmation' => [static fn () => new BookingConfirmation(new Booking()), $defaultBackoff],
            'payment reminder' => [static fn () => new PaymentReminderNotification(new Booking()), $defaultBackoff],
            'reset password' => [static fn () => new ResetPasswordNotification(new User(), 'token'), [5 * 60, 15 * 60, 30 * 60]],
            'verify email' => [static fn () => new VerifyEmailNotification(new User()), $defaultBackoff],
        ];
    }
}
