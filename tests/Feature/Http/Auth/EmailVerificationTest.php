<?php

namespace Tests\Feature\Http\Auth;

use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use App\Providers\RouteServiceProvider;
use Database\Factories\UserFactory;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(EmailVerificationNotificationController::class)]
#[CoversClass(EmailVerificationPromptController::class)]
#[CoversClass(RouteServiceProvider::class)]
#[CoversClass(UserFactory::class)]
#[CoversClass(VerifyEmailController::class)]
#[CoversClass(VerifyEmailNotification::class)]
class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanOpenEmailVerificationPrompt(): void
    {
        $this->actingAs($this->createUnverifiedUser())
            ->get('/verify-email')
            ->assertOk();
    }

    public function testUserCanRequestEmailVerificationLink(): void
    {
        Notification::fake();

        $user = $this->createUnverifiedUser();
        $this->actingAs($user)
            ->post('email/verification-notification', [
                'email' => $user->email,
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        Notification::assertSentTo($user, VerifyEmailNotification::class, static function ($notification) use ($user) {
            return str_contains($notification->toMail($user)->render(), $user->greeting);
        });
    }

    public function testUserCanVerifyEmail(): void
    {
        $user = $this->createUnverifiedUser();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );
        $this->actingAs($user)
            ->get($verificationUrl)
            ->assertRedirect(RouteServiceProvider::HOME);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    public function testUserCannotVerifyEmailWithInvalidHash(): void
    {
        $user = $this->createUnverifiedUser();

        $verificationUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user)->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    private function createUnverifiedUser(): User
    {
        return User::factory()
            ->unverified()
            ->create();
    }
}
