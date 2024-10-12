<?php

namespace Tests\Feature\UI;

use App\Http\Controllers\UserController;
use App\Models\User;
use App\Notifications\AccountCreatedNotification;
use App\Options\Ability;
use App\Options\ActiveStatus;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserController::class)]
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testCanViewUsers(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/users', Ability::ViewUsers);
    }

    public function testUserCanBeCreated(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/users/create', Ability::CreateUsers);

        Notification::fake();

        $response = $this->post('/users', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'status' => ActiveStatus::Active->value,
        ]);
        $response->assertFound();

        $user = User::query()
            ->where('email', '=', 'test@example.com')
            ->first();
        $this->assertNotNull($user);

        Notification::assertNothingSent();
    }

    public function testUserIsNotifiedIfEnabled(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/users/create', Ability::CreateUsers);

        Notification::fake();

        $response = $this->post('/users', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'status' => ActiveStatus::Active->value,
            'send_notification' => 1,
        ]);
        $response->assertFound();

        $user = User::query()
            ->where('email', '=', 'test@example.com')
            ->first();
        $this->assertNotNull($user);

        Notification::assertSentTo($user, AccountCreatedNotification::class);
    }
}
