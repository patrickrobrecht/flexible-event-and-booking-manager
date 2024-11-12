<?php

namespace Http;

use App\Http\Controllers\AccountController;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Options\Ability;
use App\Policies\UserPolicy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(AccountController::class)]
#[CoversClass(UserPolicy::class)]
#[CoversClass(UserRequest::class)]
class AccountControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testAccountIsAccessibleWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/account', Ability::ViewAccount);
    }

    public function testEditAccountIsAccessibleWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/account/edit', Ability::EditAccount);
    }

    public function testAccountCannotBeEditedWithoutAbility(): void
    {
        $this->actingAsUserWithAbility(Ability::ViewAccount);

        $this->put('/account', $this->getRandomUserData())->assertForbidden();
    }

    public function testAccountCanBeEditedWithAbility(): void
    {
        $this->actingAsUserWithAbility(Ability::EditAccount);

        $this->put('/account', $this->getRandomUserData())
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect('/account/edit');
    }

    public function testAccountEditThrowsErrorsForInvalidData(): void
    {
        $this->actingAsUserWithAbility(Ability::EditAccount);

        $data = array_replace($this->getRandomUserData(), ['first_name' => null]);
        $this->put('/account', $data)
            ->assertSessionHasErrors([
                'first_name' => 'Vorname muss ausgefüllt werden.',
            ]);
    }

    private function getRandomUserData(): array
    {
        return array_intersect_key(
            User::factory()->unverified()->raw(),
            array_flip(['first_name', 'last_name', 'street', 'house_number', 'postal_code', 'city', 'email'])
        );
    }

    public function testListOfOwnAbilitiesIsAccessibleWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/account/abilities', Ability::ViewAbilities);
    }
}
