<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\OrganizationController;
use App\Http\Requests\Filters\OrganizationFilterRequest;
use App\Http\Requests\OrganizationRequest;
use App\Models\Organization;
use App\Options\Ability;
use App\Options\FilterValue;
use App\Policies\OrganizationPolicy;
use Database\Factories\OrganizationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(FilterValue::class)]
#[CoversClass(Organization::class)]
#[CoversClass(OrganizationController::class)]
#[CoversClass(OrganizationFactory::class)]
#[CoversClass(OrganizationFilterRequest::class)]
#[CoversClass(OrganizationPolicy::class)]
#[CoversClass(OrganizationRequest::class)]
class OrganizationControllerTest extends TestCase
{
    use GeneratesTestData;
    use RefreshDatabase;

    public function testOrganizationsCanBeListedWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility('/organizations', Ability::ViewOrganizations);
    }

    public function testSingleOrganizationIsAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility("/organizations/{$this->createRandomOrganization()->id}", Ability::ViewOrganizations);
    }

    public function testCreateOrganizationFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility('/organizations/create', Ability::CreateOrganizations);
    }

    public function testEditOrganizationFormIsAccessibleOnlyWithCorrectAbility(): void
    {
        $this->assertRouteOnlyAccessibleWithAbility("/organizations/{$this->createRandomOrganization()->id}/edit", Ability::EditOrganizations);
    }

    private function createRandomOrganization(): Organization
    {
        return self::createOrganization();
    }
}
