<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\OrganizationController;
use App\Http\Requests\Filters\OrganizationFilterRequest;
use App\Http\Requests\OrganizationRequest;
use App\Models\Location;
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

    public function testUserCanViewOrganizationsOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/organizations', Ability::ViewOrganizations);
    }

    public function testOrganizationsAreShown(): void
    {
        $organizations = Organization::factory()
            ->for(Location::factory()->create())
            ->count(5)
            ->create();

        $this->actingAsUserWithAbility(Ability::ViewOrganizations);

        $response = $this->get('/organizations')->assertOk();
        $organizations->each(fn (Organization $organization) => $response->assertSee($organization->name));
    }

    public function testUserCanViewSingleOrganizationOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility("/organizations/{$this->createRandomOrganization()->slug}", Ability::ViewOrganizations);
    }

    public function testUserCanOpenCreateOrganizationFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/organizations/create', Ability::CreateOrganizations);
    }

    public function testUserCanStoreOrganizationOnlyWithCorrectAbility(): void
    {
        $locations = Location::factory()->count(5)->create();
        $organization = Organization::factory()->makeOne();
        $data = [
            ...$organization->toArray(),
            'location_id' => $this->faker->randomElement($locations)->id,
        ];

        $this->assertUserCanPostOnlyWithAbility('organizations', $data, Ability::CreateOrganizations, null);
    }

    public function testUserCanOpenEditOrganizationFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility("/organizations/{$this->createRandomOrganization()->slug}/edit", Ability::EditOrganizations);
    }

    public function testUserCanUpdateOrganizationOnlyWithCorrectAbility(): void
    {
        $organization = $this->createRandomOrganization();
        $data = [
            ...Organization::factory()->makeOne()->toArray(),
            'location_id' => $this->faker->randomElement(Location::factory()->count(5)->create())->id,
        ];

        $this->assertUserCanPutOnlyWithAbility(
            "/organizations/{$organization->slug}",
            $data,
            Ability::EditOrganizations,
            "/organizations/{$organization->slug}/edit",
            "/organizations/{$data['slug']}/edit"
        );
    }

    private function createRandomOrganization(): Organization
    {
        return self::createOrganization();
    }
}
