<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\UserRoleController;
use App\Http\Requests\Filters\UserRoleFilterRequest;
use App\Http\Requests\UserRoleRequest;
use App\Models\UserRole;
use App\Options\Ability;
use App\Options\AbilityGroup;
use App\Options\FilterValue;
use App\Policies\UserRolePolicy;
use Database\Factories\UserRoleFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(FilterValue::class)]
#[CoversClass(Ability::class)]
#[CoversClass(AbilityGroup::class)]
#[CoversClass(UserRole::class)]
#[CoversClass(UserRoleController::class)]
#[CoversClass(UserRoleFactory::class)]
#[CoversClass(UserRoleFilterRequest::class)]
#[CoversClass(UserRolePolicy::class)]
#[CoversClass(UserRoleRequest::class)]
class UserRoleControllerTest extends TestCase
{
    use RefreshDatabase;

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
        $data = UserRole::factory()->makeOne()->toArray();

        $this->assertUserCanPostOnlyWithAbility('user-roles', $data, Ability::CreateUserRoles, null);
    }

    public function testUserCanOpenEditUserRoleFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility("/user-roles/{$this->createRandomUserRole()->id}/edit", Ability::EditUserRoles);
    }

    private function createRandomUserRole(): UserRole
    {
        return UserRole::factory()->create();
    }
}
