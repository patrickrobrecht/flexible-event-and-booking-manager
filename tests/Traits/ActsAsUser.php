<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\UserRole;
use App\Options\Ability;
use App\Options\ActiveStatus;
use Database\Factories\UserFactory;
use Database\Seeders\UserRoleSeeder;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\CoversClass;

/**
 * @mixin Assert
 */
#[CoversClass(Ability::class)]
#[CoversClass(User::class)]
#[CoversClass(UserFactory::class)]
#[CoversClass(UserRole::class)]
trait ActsAsUser
{
    use InteractsWithDatabase;
    use RefreshDatabase;
    use WithFaker;

    protected $userRoles = [];

    protected function actingAsAdmin(): User
    {
        return $this->actingAsUserWithRoleName('Administrator');
    }

    protected function actingAsAnyUser(): User
    {
        $user = User::factory()->status(ActiveStatus::Active)->create();
        $this->actingAs($user);

        return $user;
    }

    protected function actingAsUserWithAbility(Ability|array $ability): User
    {
        return $this->actingAsUserWithRole($this->createUserRoleWithAbility($ability));
    }

    protected function actingAsUserWithFullAbilitiesExcept(Ability|array $ability): User
    {
        return $this->actingAsUserWithRole($this->createUserRoleWithoutAbility($ability));
    }

    protected function actingAsUserWithRole(UserRole $userRole): User
    {
        $userWithRole = User::factory()
            ->hasAttached($userRole)
            ->create();
        $this->assertNotNull($userWithRole);

        $this->actingAs($userWithRole);
        return $userWithRole;
    }

    protected function actingAsUserWithRoleName(string $roleName): User
    {
        $this->seed(UserRoleSeeder::class);
        $adminRole = UserRole::query()
            ->where('name', '=', $roleName)
            ->first();

        return $this->actingAsUserWithRole($adminRole);
    }

    protected function assertGuestCanGet(string $route): void
    {
        Auth::logout(); // to make sure we act as a guest
        $this->get($route)->assertOk();
    }

    protected function assertGuestCannotGet(string $route, bool $redirectedToLogin = true): void
    {
        Auth::logout(); // to make sure we act as a guest
        $response = $this->get($route);
        if ($redirectedToLogin) {
            $response->assertFound()->assertRedirect('/login');
        } else {
            $response->assertForbidden();
        }
    }

    protected function assertUserCan(User $user, string $ability, mixed $arguments): void
    {
        self::assertTrue($user->can($ability, $arguments), "Failed to assert user can {$ability}");
    }

    protected function assertUserCannot(User $user, string $ability, mixed $arguments): void
    {
        self::assertTrue($user->cannot($ability, $arguments), "Failed to assert user cannot {$ability}");
    }

    protected function assertUserCanDeleteOnlyWithAbility(string $route, Ability|array $ability, ?string $redirectRoute): void
    {
        // Cannot delete with all abilities except the required ones.
        $this->actingAsUserWithFullAbilitiesExcept($ability);
        $this->delete($route)
            ->assertForbidden();

        // Can delete with correct ability.
        $this->actingAsUserWithAbility($ability);
        $this->delete($route)
            ->assertRedirect($redirectRoute);
    }

    protected function assertUserCanGetOnlyWithAbility(string $route, Ability|array $ability, bool $guestRedirectedToLogin = true): void
    {
        $this->assertGuestCannotGet($route, $guestRedirectedToLogin);
        $this->assertUserCannotGetWithoutAbility($route, $ability);

        $this->assertUserCanGetWithAbility($route, $ability);
    }

    protected function assertUserCanGetWithAbility(string $route, Ability|array $ability): void
    {
        $this->actingAsUserWithAbility($ability);
        $this->get($route)->assertOk();
    }

    protected function assertUserCannotGetDespiteAbility(string $route, Ability $ability): void
    {
        $this->actingAsUserWithAbility($ability);
        $this->get($route)->assertForbidden();
    }

    protected function assertUserCannotGetWithoutAbility(string $route, Ability|array $ability): void
    {
        $this->actingAsUserWithFullAbilitiesExcept($ability);
        $this->get($route)->assertForbidden();
    }

    protected function assertUserCanPostOnlyWithAbility(string $route, array $data, Ability|array $ability, ?string $redirectRoute): void
    {
        // Cannot submit POST request with all abilities except the required ones.
        $this->actingAsUserWithFullAbilitiesExcept($ability);
        $this->post($route, $data)
            ->assertForbidden();

        $this->assertUserCanPostWithAbility($route, $data, $ability, $redirectRoute);
    }

    protected function assertUserCanPostWithAbility(string $route, array $data, Ability|array $ability, ?string $redirectRoute): void
    {
        $this->actingAsUserWithAbility($ability);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->post($route, $data)
            ->assertSessionHasNoErrors()
            ->assertRedirect($redirectRoute);
    }

    protected function assertUserCanPutOnlyWithAbility(string $route, array $data, Ability|array $ability, ?string $redirectRoute): void
    {
        // Cannot submit PUT request with all abilities except the required ones.
        $this->actingAsUserWithFullAbilitiesExcept($ability);
        $this->put($route, $data)
            ->assertForbidden();

        $this->assertUserCanPutWithAbility($route, $data, $ability, $redirectRoute);
    }

    protected function assertUserCanPutWithAbility(string $route, array $data, Ability|array $ability, ?string $redirectRoute): void
    {
        $this->actingAsUserWithAbility($ability);
        $this->put($route, $data)
            ->assertRedirect($redirectRoute);
    }

    protected function createUserRoleWithAbility(Ability|array $ability): UserRole
    {
        if (is_array($ability)) {
            $abilities = $ability; #
            $userRoleName = 'With ' . implode(', ', Ability::values($ability));
        } else {
            $abilities = [$ability];
            $userRoleName = 'With ' . $ability->value;
        }

        if (!isset($this->userRoles[$userRoleName])) {
            $this->userRoles[$userRoleName] = new UserRole([
                'name' => $userRoleName,
                'abilities' => $abilities,
            ]);
            $this->userRoles[$userRoleName]->save();
        }

        return $this->userRoles[$userRoleName];
    }

    protected function createUserRoleWithoutAbility(Ability|array $ability): UserRole
    {
        $userRoleName = 'Without ' . (
            is_array($ability)
                ? implode(', ', Ability::values($ability))
                : $ability->value
        );
        if (!isset($this->userRoles[$userRoleName])) {
            $this->userRoles[$userRoleName] = new UserRole([
                'name' => $userRoleName,
                'abilities' => Ability::casesExcept($ability),
            ]);
            $this->userRoles[$userRoleName]->save();
        }

        return $this->userRoles[$userRoleName];
    }
}
