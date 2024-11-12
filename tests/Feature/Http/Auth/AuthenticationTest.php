<?php

namespace Tests\Feature\Http\Auth;

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use App\Options\ActiveStatus;
use App\Providers\RouteServiceProvider;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(ActiveStatus::class)]
#[CoversClass(AuthenticatedSessionController::class)]
#[CoversClass(LoginRequest::class)]
#[CoversClass(User::class)]
#[CoversClass(UserFactory::class)]
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function testGuestCanViewLoginForm(): void
    {
        $this->assertGuestCanGet('/login');
    }

    public function testActiveUserCanLogin(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    #[DataProvider('notActiveStatuses')]
    public function testInactiveUserCannotLogin(ActiveStatus $activeStatus): void
    {
        $user = User::factory()
            ->status($activeStatus)
            ->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public static function notActiveStatuses(): array
    {
        return [
            [ActiveStatus::Inactive],
            [ActiveStatus::Archived],
        ];
    }

    public function testUserCannotLoginWithWrongPassword(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function testUserCanLogout(): void
    {
        $this->actingAsAnyUser();

        $this->post(route('logout'))
            ->assertRedirect('/');
        $this->assertGuest();
    }
}
