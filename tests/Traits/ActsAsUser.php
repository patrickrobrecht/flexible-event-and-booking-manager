<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\UserRole;
use App\Options\Ability;
use Database\Seeders\UserRoleSeeder;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Auth;

trait ActsAsUser
{
    use InteractsWithDatabase;
    use RefreshDatabase;
    use WithFaker;

    protected function actingAsAdmin(): User
    {
        return $this->actingAsUserWithRoleName('Administrator');
    }

    protected function actingAsUserWithAbility(Ability $ability): User
    {
        $userRole = new UserRole([
            'name' => 'User ' . $ability->value,
            'abilities' => [$ability],
        ]);
        $userRole->save();

        return $this->actingAsUserWithRole($userRole);
    }

    protected function actingAsUserWithFullAbilitiesExcept(Ability $ability): User
    {
        $userRole = new UserRole([
            'name' => 'User without ' . $ability->value,
            'abilities' => Ability::casesExcept($ability),
        ]);
        $userRole->save();

        return $this->actingAsUserWithRole($userRole);
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

    protected function assertRouteAccessibleAsGuest(string $route): void
    {
        Auth::logout(); // to make sure we act as a guest
        $this->get($route)->assertOk();
    }

    protected function assertRouteAccessibleWithAbility(string $route, Ability $ability): void
    {
        $this->actingAsUserWithAbility($ability);
        $this->get($route)->assertOk();
    }

    protected function assertRouteNotAccessibleAsGuest(string $route): void
    {
        Auth::logout(); // to make sure we act as a guest
        $this->get($route)->assertFound()->assertRedirect('/login');
    }

    protected function assertRouteNotAccessibleWithoutAbility(string $route, Ability $ability): void
    {
        $this->actingAsUserWithFullAbilitiesExcept($ability);
        $this->get($route)->assertForbidden();
    }

    protected function assertRouteOnlyAccessibleOnlyWithAbility(string $route, Ability $ability): void
    {
        $this->assertRouteNotAccessibleAsGuest($route);
        $this->assertRouteNotAccessibleWithoutAbility($route, $ability);
        $this->assertRouteAccessibleWithAbility($route, $ability);
    }
}
