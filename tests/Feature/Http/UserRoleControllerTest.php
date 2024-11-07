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

    public function testUserRolesCanBeListedWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility('/user-roles', Ability::ViewUserRoles);
    }

    public function testUserRoleIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility("/user-roles/{$this->createRandomUserRole()->id}", Ability::ViewUserRoles);
    }

    public function testCreateUserRoleFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility('/user-roles/create', Ability::CreateUserRoles);
    }

    public function testEditUserRoleFormIsAccessibleOnlyWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility("/user-roles/{$this->createRandomUserRole()->id}/edit", Ability::EditUserRoles);
    }

    private function createRandomUserRole(): UserRole
    {
        return UserRole::factory()->create();
    }
}
