<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\AbilityGroup;
use App\Enums\FilterValue;
use App\Http\Controllers\UserRoleController;
use App\Http\Requests\Filters\UserRoleFilterRequest;
use App\Http\Requests\UserRoleRequest;
use App\Models\User;
use App\Models\UserRole;
use App\Policies\UserRolePolicy;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Ability::class)]
#[CoversClass(AbilityGroup::class)]
#[CoversClass(FilterValue::class)]
#[CoversClass(User::class)]
#[CoversClass(UserRole::class)]
#[CoversClass(UserRoleController::class)]
#[CoversClass(UserRoleFilterRequest::class)]
#[CoversClass(UserRolePolicy::class)]
#[CoversClass(UserRoleRequest::class)]
class UserRoleControllerTest extends TestCase
{
    public function testUserCanViewUserRolesOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/user-roles', Ability::ViewUserRoles);
    }

    public function testUserCanViewSingleUserRoleOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility("/user-roles/{$this->createRandomUserRole()->id}", Ability::ViewUserRoles);
    }

    public function testUserCanOpenCreateUserRoleFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/user-roles/create', Ability::CreateUserRoles);
    }

    public function testUserCanStoreUserRoleOnlyWithCorrectAbility(): void
    {
        $data = self::makeData(UserRole::factory());

        $this->assertUserCanPostOnlyWithAbility('/user-roles', $data, Ability::CreateUserRoles, null);
    }

    public function testUserCanOpenEditUserRoleFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility("/user-roles/{$this->createRandomUserRole()->id}/edit", Ability::EditUserRoles);
    }

    public function testUserCanUpdateUserRoleOnlyWithCorrectAbility(): void
    {
        $userRole = $this->createRandomUserRole();
        $data = self::makeData(UserRole::factory());

        $this->assertUserCanPutOnlyWithAbility(
            "/user-roles/{$userRole->id}",
            $data,
            Ability::EditUserRoles,
            "/user-roles/{$userRole->id}/edit",
            "/user-roles/{$userRole->id}"
        );
    }

    public function testUserCanDeleteUserRoleOnlyWithCorrectAbility(): void
    {
        $userRole = $this->createRandomUserRole();
        $user = self::createUserWithUserRole($userRole);

        $this->assertDatabaseHas('user_roles', ['id' => $userRole->id]);
        $this->assertDatabaseHas('user_user_role', ['user_id' => $user->id, 'user_role_id' => $userRole->id]);
        $this->assertUserCanDeleteOnlyWithAbility("/user-roles/{$userRole->id}", Ability::DestroyUserRoles, '/user-roles');
        $this->assertDatabaseMissing('user_roles', ['id' => $userRole->id]);
        $this->assertDatabaseMissing('user_user_role', ['user_id' => $user->id, 'user_role_id' => $userRole->id]);
    }

    private function createRandomUserRole(): UserRole
    {
        return UserRole::factory()->create();
    }
}
