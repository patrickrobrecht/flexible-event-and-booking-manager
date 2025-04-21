<?php

namespace Tests\Feature\Http\Api;

use App\Enums\Ability;
use App\Exceptions\Handler;
use App\Http\Controllers\Api\OrganizationApiController;
use App\Http\Requests\Filters\OrganizationFilterRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use App\Models\QueryBuilder\SortOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Organization::class)]
#[CoversClass(OrganizationApiController::class)]
#[CoversClass(OrganizationFilterRequest::class)]
#[CoversClass(OrganizationResource::class)]
#[CoversClass(Handler::class)]
#[CoversClass(SortOptions::class)]
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

    public function testNotExistingOrganisationSlugResultsInNotFound(): void
    {
        $this->assertTokenCannotGetDespiteAbility('api/organizations/not-existing-slug', Ability::ViewEvents, Response::HTTP_NOT_FOUND)
            ->assertJsonFragment([
                'message' => 'Organization not-existing-slug do not exist.',
            ]);
    }
}
