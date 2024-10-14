<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\UserRoleController;
use App\Models\UserRole;
use App\Options\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(UserRoleController::class)]
class UserRoleControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserRolesCanBeListedWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/user-roles', Ability::ViewUserRoles);
    }

    public function testUserRoleIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility("/user-roles/{$this->createRandomUserRole()->id}", Ability::ViewUserRoles);
    }

    public function testCreateUserRoleFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/user-roles/create', Ability::CreateUserRoles);
    }

    public function testEditUserRoleFormIsAccessibleOnlyWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility("/user-roles/{$this->createRandomUserRole()->id}/edit", Ability::EditUserRoles);
    }

    private function createRandomUserRole(): UserRole
    {
        return UserRole::factory()->create();
    }
}
