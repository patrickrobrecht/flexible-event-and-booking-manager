<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\OrganizationController;
use App\Models\Location;
use App\Models\Organization;
use App\Options\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(OrganizationController::class)]
class OrganizationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function tesOrganizationsCanBeListedWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/organizations', Ability::ViewOrganizations);
    }

    public function testSingleOrganizationIsAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility("/organizations/{$this->createRandomOrganization()->id}", Ability::ViewOrganizations);
    }

    public function testCreateOrganizationFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility('/organizations/create', Ability::CreateOrganizations);
    }

    public function testEditOrganizationFormIsAccessibleOnlyWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleOnlyWithAbility("/organizations/{$this->createRandomOrganization()->id}/edit", Ability::EditOrganizations);
    }

    private function createRandomOrganization(): Organization
    {
        return Organization::factory()
            ->for(Location::factory()->create())
            ->create();
    }
}
