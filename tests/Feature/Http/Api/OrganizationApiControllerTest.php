<?php

namespace Tests\Feature\Http\Api;

use App\Enums\Ability;
use App\Models\Organization;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;
use Tests\Traits\GeneratesTestData;

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

    public function testSingleOrganizationCanBeRequestedWithIncludes(): void
    {
        $organization = self::createOrganization();

        $this->withHeadersForApiRequestWithAbility(Ability::ViewOrganizations)
            ->getJson("api/organizations/{$organization->slug}?include=location")
            ->assertOk()
            ->assertJsonPath('data.location.street', $organization->location->street);
    }

    public function testNotExistingOrganisationSlugResultsInNotFound(): void
    {
        $this->assertTokenCannotGetDespiteAbility('api/organizations/not-existing-slug', Ability::ViewEvents, Response::HTTP_NOT_FOUND)
            ->assertJsonFragment([
                'message' => 'Organization not-existing-slug do not exist.',
            ]);
    }
}
