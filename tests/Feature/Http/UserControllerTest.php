<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\ActiveStatus;
use App\Enums\FilterValue;
use App\Http\Controllers\UserController;
use App\Http\Requests\Filters\UserFilterRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Notifications\AccountCreatedNotification;
use App\Policies\UserPolicy;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(AccountCreatedNotification::class)]
#[CoversClass(ActiveStatus::class)]
#[CoversClass(FilterValue::class)]
#[CoversClass(User::class)]
#[CoversClass(UserController::class)]
#[CoversClass(UserFactory::class)]
#[CoversClass(UserFilterRequest::class)]
#[CoversClass(UserPolicy::class)]
#[CoversClass(UserRequest::class)]
class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewUsersOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/users', Ability::ViewUsers);
    }

    public function testUserCanViewSingleUserOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility("/users/{$this->createRandomUser()->id}", Ability::ViewUsers);
    }

    public function testUserCanOpenCreateUserFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/users/create', Ability::CreateUsers);
    }

    public function testUserCanStoreUserWithCorrectAbility(): void
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

    public function testUserCanStoreUserWithCorrectAbilityAndCreatedUserIsNotifiedIfEnabled(): void
    {
        $adminUser = $this->actingAsUserWithAbility(Ability::CreateUsers);

        Notification::fake();

        $response = $this->post('/users', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'status' => ActiveStatus::Active->value,
            'send_notification' => 1,
        ]);
        $response->assertFound();

        $createdUser = User::query()
            ->where('email', '=', 'test@example.com')
            ->first();
        $this->assertNotNull($createdUser);

        Notification::assertSentTo($createdUser, AccountCreatedNotification::class, static function ($notification) use ($adminUser, $createdUser) {
            $emailContent = $notification->toMail($createdUser)->render();
            return str_contains($emailContent, $createdUser->greeting) && str_contains($emailContent, $adminUser->name);
        });
    }

    public function testUserCanOpenEditUserFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility("/users/{$this->createRandomUser()->id}/edit", Ability::EditUsers);
    }

    public function testUserCanUpdateUserOnlyWithCorrectAbility(): void
    {
        $user = $this->createRandomUser();
        $data = User::factory()->makeOne()->toArray();

        $editRoute = "/users/{$user->id}/edit";
        $this->assertUserCanPutOnlyWithAbility("/users/{$user->id}", $data, Ability::EditUsers, $editRoute, $editRoute);
    }

    private function createRandomUser(): User
    {
        return User::factory()->create();
    }
}
