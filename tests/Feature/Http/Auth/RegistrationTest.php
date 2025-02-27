<?php

namespace Tests\Feature\Http\Auth;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use App\Notifications\VerifyEmailNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(RegisteredUserController::class)]
#[CoversClass(RegisterRequest::class)]
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestCannotOpenRegistrationFormIfRegistrationIsDisabled(): void
    {
        Config::set('app.features.registration', false);

        $this->get('/register')->assertForbidden();
    }

    public function testGuestCanOpenRegistrationFormIfRegistrationIsEnabled(): void
    {
        Config::set('app.features.registration', true);

        $this->assertGuestCanGet('/register');
    }

    public function testNewUserCanRegister(): void
    {
        Config::set('app.features.registration', true);
        Config::set('app.urls.terms_and_conditions', '');

        Notification::fake();

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect('/');

        $registeredUser = User::query()
            ->where('email', 'test@example.com')
            ->first();
        $this->assertNotNull($registeredUser);

        Notification::assertSentTo($registeredUser, VerifyEmailNotification::class, static function ($notification) use ($registeredUser) {
            return str_contains($notification->toMail($registeredUser)->render(), $registeredUser->greeting);
        });
    }

    public function testNewUserCannotRegisterWithoutAcceptingTheTerms(): void
    {
        Config::set('app.features.registration', true);

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }
}
