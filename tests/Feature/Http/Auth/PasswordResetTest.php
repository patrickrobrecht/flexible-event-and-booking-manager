<?php

namespace Tests\Feature\Http\Auth;

use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(NewPasswordController::class)]
#[CoversClass(PasswordResetLinkController::class)]
#[CoversClass(ResetPasswordNotification::class)]
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestCanOpenResetPasswordForm(): void
    {
        $this->assertGuestCanGet('/forgot-password');
    }

    public function testUserCanRequestResetPasswordLink(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $this->get('/reset-password/' . $notification->token)->assertOk();

            return str_contains($notification->toMail($user)->render(), $user->greeting);
        });
    }

    public function testUserCanResetPasswordWithValidToken(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }

    public function testUserCannotResetPasswordWithInvalidToken(): void
    {
        $user = User::factory()->create();
        $this
            ->post('/reset-password', [
                'token' => 'wrong-token',
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertSessionHasErrors()
            ->assertRedirect();
    }
}
