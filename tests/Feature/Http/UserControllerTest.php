<?php

namespace Tests\Feature\Http;

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

    public function testUsersCanBeListedWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/users', Ability::ViewUsers);
    }

    public function testUserIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility("/users/{$this->createRandomUser()->id}", Ability::ViewUsers);
    }

    public function testCreateUserFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/users/create', Ability::CreateUsers);
    }

    public function testUserIsStored(): void
    {
        $this->actingAsUserWithAbility(Ability::CreateUsers);

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

    public function testUserIsStoredAndNotifiedIfEnabled(): void
    {
        $this->actingAsUserWithAbility(Ability::CreateUsers);

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

    public function testEditUserFormIsAccessibleOnlyWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility("/users/{$this->createRandomUser()->id}/edit", Ability::EditUsers);
    }

    private function createRandomUser(): User
    {
        return User::factory()->create();
    }
}
