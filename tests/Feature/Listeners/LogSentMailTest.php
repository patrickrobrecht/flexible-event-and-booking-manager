<?php

namespace Tests\Feature\Listeners;

use App\Listeners\LogSentMail;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(LogSentMail::class)]
class LogSentMailTest extends TestCase
{
    use RefreshDatabase;

    public function testSentMailIsLogged(): void
    {
        $user = User::factory()->create();

        Log::shouldReceive('info')
            ->once()
            ->with("Verification email queued for {$user->email}");
        Log::shouldReceive('info')
            ->once()
            ->withArgs(static fn (string $message, array $context) => str_starts_with($message, 'Sent mail "')
                && str_ends_with($message, "\" to {$user->email}")
                && $context['notification'] === VerifyEmailNotification::class
                && is_string($context['message_id']));

        $user->sendEmailVerificationNotification();
    }
}
