<?php

namespace Feature\Http\Api;

use App\Enums\Ability;
use App\Http\Controllers\Api\OrganizationApiController;
use App\Http\Requests\Filters\OrganizationFilterRequest;
use App\Models\Organization;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;
use Tests\Traits\GeneratesTestData;

#[CoversClass(OrganizationApiController::class)]
#[CoversClass(OrganizationFilterRequest::class)]
class OrganizationApiControllerTest extends TestCase
{
    use ActsWithToken;
    use GeneratesTestData;

    public function testOrganizationsCanBeRequestedOnlyWithCorrectAbility(): void
    {
        $this->createCollection(Organization::factory()->forLocation());

        $this->assertTokenCanGetOnlyWithAbility('api/organizations', Ability::ViewOrganizations);
    }

    public function testSingleOrganizationCanBeRequestedOnlyWithCorrectAbility(): void
    {
        $organization = self::createOrganization();

        $this->assertTokenCanGetOnlyWithAbility("api/organizations/{$organization->slug}", Ability::ViewOrganizations);
    }
}
