<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\ActiveStatus;
use App\Enums\FilterValue;
use App\Http\Controllers\UserController;
use App\Http\Requests\Filters\UserFilterRequest;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\UserRole;
use App\Notifications\AccountCreatedNotification;
use App\Policies\UserPolicy;
use Closure;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(AccountCreatedNotification::class)]
#[CoversClass(ActiveStatus::class)]
#[CoversClass(FilterValue::class)]
#[CoversClass(User::class)]
#[CoversClass(UserController::class)]
#[CoversClass(UserFilterRequest::class)]
#[CoversClass(UserPolicy::class)]
#[CoversClass(UserRequest::class)]
#[CoversClass(UserRole::class)]
class UserControllerTest extends TestCase
{
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

    public function testUserCanStoreUserOnlyWithCorrectAbility(): void
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
        self::assertNotNull($user);

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
        self::assertNotNull($createdUser);

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
        $data = self::makeData(User::factory());

        $this->assertUserCanPutOnlyWithAbility(
            "/users/{$user->id}",
            $data,
            Ability::EditUsers,
            "/users/{$user->id}/edit",
            "/users/{$user->id}"
        );
    }

    public function testUserCanDeleteUsersOnlyWithCorrectAbility(): void
    {
        $userRole = UserRole::factory()->create();
        $user = self::createUserWithUserRole($userRole);

        $this->assertDatabaseHas('users', ['id' => $user->id]);
        $this->assertDatabaseHas('user_user_role', ['user_role_id' => $userRole->id, 'user_id' => $user->id]);
        $this->assertUserCanDeleteOnlyWithAbility("/users/{$user->id}", Ability::DestroyUsers, '/users');
        $this->assertDatabaseMissing('user_user_role', ['user_role_id' => $userRole->id, 'user_id' => $user->id]);
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function testUserCannotDeleteHimself(): void
    {
        $user = $this->actingAsUserWithAbility(Ability::DestroyUsers);

        $this->delete("/users/{$user->id}")
            ->assertForbidden()
            ->assertSee('kann nicht gelöscht werden, da es sich um das eigene Konto handelt.');
    }

    /**
     * @param Closure(): User $userProvider
     */
    #[DataProvider('usersWithReferences')]
    public function testUserCannotBeDeletedBecauseOfReferences(Closure $userProvider, string $message): void
    {
        $user = $userProvider();

        $this->assertUserCannotDeleteDespiteAbility("/users/{$user->id}", [Ability::ViewUsers, Ability::DestroyUsers], null)
            ->assertSee($message);
    }

    /**
     * @return array<int, array{Closure(): User, string}>
     */
    public static function usersWithReferences(): array
    {
        return [
            [function () {
                /** @var User $user */
                $user = self::createBooking()->bookedByUser;
                return $user;
            }, 'kann nicht gelöscht werden, weil er/sie 1 Anmeldung hat.'],
            [fn () => self::createDocument(static fn () => self::createEvent())->uploadedByUser, 'kann nicht gelöscht werden, weil er/sie 1 Dokument hochgeladen hat.'],
            [fn () => self::createUserResponsibleFor(self::createEvent()), 'kann nicht gelöscht werden, weil er/sie für 1 Veranstaltung verantwortlich ist.'],
            [fn () => self::createUserResponsibleFor(self::createEventSeries()), 'kann nicht gelöscht werden, weil er/sie für 1 Veranstaltungsreihe verantwortlich ist.'],
            [fn () => self::createUserResponsibleFor(self::createOrganization()), 'kann nicht gelöscht werden, weil er/sie für 1 Organisation verantwortlich ist.  '],
        ];
    }

    private function createRandomUser(): User
    {
        return User::factory()->create();
    }
}
