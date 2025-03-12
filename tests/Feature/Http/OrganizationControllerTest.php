<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\FilterValue;
use App\Http\Controllers\OrganizationController;
use App\Http\Requests\Filters\OrganizationFilterRequest;
use App\Http\Requests\OrganizationRequest;
use App\Models\BookingOption;
use App\Models\Document;
use App\Models\Event;
use App\Models\Location;
use App\Models\Organization;
use App\Policies\OrganizationPolicy;
use Closure;
use Database\Factories\OrganizationFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Document::class)]
#[CoversClass(Event::class)]
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
            ->for(self::createLocation())
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

    /**
     * @param  Closure(): OrganizationFactory  $organizationProvider
     * @param  Closure(): OrganizationFactory  $dataProvider
     */
    #[DataProvider('updateBankAccountCases')]
    public function testUserCannotUpdateOrganizationAndRemoveIfPaidBookingOptionsExist(
        Closure $organizationProvider,
        Closure $dataProvider,
        bool $ok
    ): void {
        $organization = $organizationProvider()
            ->for(self::createLocation())
            ->create();
        $data = [
            ...$dataProvider()->makeOne()->toArray(),
            'location_id' => $organization->location_id,
        ];

        $this->actingAsUserWithAbility(Ability::EditOrganizations);
        $testResponse = $this->put("/organizations/{$organization->slug}", $data)
            ->assertRedirect();
        $ok
            ? $testResponse->assertSessionHasNoErrors()
            : $testResponse->assertSessionHasErrors([
                'iban',
                'bank_name',
            ]);
    }

    public static function updateBankAccountCases(): array
    {
        return [
            [
                // A bank account can be added for any organization.
                fn () => Organization::factory(),
                fn () => Organization::factory()->withBankAccount(),
                true,
            ],
            [
                // An organization without events does not require a bank account details, so they can be removed.
                fn () => Organization::factory()->withBankAccount(),
                fn () => Organization::factory(),
                true,
            ],
            [
                // An organization with events having only unpaid booking options does not require a bank account, so user is not forced to enter them.
                fn () => Organization::factory()
                    ->has(
                        Event::factory()
                            ->for(self::createLocation())
                            ->for(self::createOrganization())
                            ->has(BookingOption::factory()->withoutPrice())
                    ),
                fn () => Organization::factory(),
                true,
            ],
            [
                // An organization with paid booking requires bank account.
                fn () => Organization::factory()
                    ->withBankAccount()
                    ->has(
                        Event::factory()
                            ->for(self::createLocation())
                            ->for(self::createOrganization())
                            ->has(BookingOption::factory())
                    ),
                fn () => Organization::factory(),
                false,
            ],
        ];
    }

    private function createRandomOrganization(): Organization
    {
        return self::createOrganization();
    }
}
