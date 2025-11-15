<?php

namespace Tests\Feature\Http\Auth;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
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

        $data = $this->registrationData();
        $response = $this->post('/register', $data);

        $this->assertAuthenticated();
        $response->assertRedirect('/');

        $registeredUser = User::query()
            ->where('email', $data['email'])
            ->first();
        $this->assertNotNull($registeredUser);
    }

    public function testNewUserCannotRegisterWithoutAcceptingTheTerms(): void
    {
        Config::set('app.features.registration', true);

        $response = $this->post('/register', $this->registrationData());

        $response->assertStatus(302);
        $response->assertSessionHasErrors([
            'terms_and_conditions' => 'Die AGB müssen akzeptiert werden. Sonst ist eine Registrierung nicht möglich.',
        ]);
    }

    public function testRegistrationsAreRateLimited(): void
    {
        Config::set('app.features.registration', true);
        Config::set('app.urls.terms_and_conditions', '');

        // Make the maximum number of attempts.
        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $this->post('/register', $this->registrationData())
                ->assertRedirect();
        }

        // Make an additional request and assert status code 429.
        $this->post('/register', $this->registrationData())
            ->assertTooManyRequests();
    }

    /**
     * @return array<string, mixed>
     */
    private function registrationData(): array
    {
        /** @var User $user */
        $user = User::factory()->makeOne();

        return [
            'start_time' => (int) Carbon::now()->timestamp - 6,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ];
    }
}
