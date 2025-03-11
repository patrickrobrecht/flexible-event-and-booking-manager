<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Http\Controllers\AccountController;
use App\Http\Requests\UserRequest;
use App\Models\User;
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

    public function testUserCanViewAccountOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/account', Ability::ViewAccount);
    }

    public function testUserCanViewAbilitiesOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/account/abilities', Ability::ViewAbilities);
    }

    public function testUserCanOpenEditAccountFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/account/edit', Ability::EditAccount);
    }

    public function testUserCanUpdateAccountWithCorrectAbility(): void
    {
        $this->actingAsUserWithAbility(Ability::EditAccount);

        $this->from('/account/edit')
            ->put('/account', $this->getRandomUserData())
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect('/account/edit');
    }

    public function testUserCannotUpdateAccountWithoutAbility(): void
    {
        $this->actingAsUserWithAbility(Ability::ViewAccount);

        $this->put('/account', $this->getRandomUserData())->assertForbidden();
    }

    public function testUserReceivesErrorMessagesForInvalidAccountData(): void
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
            User::factory()->raw(),
            array_flip(['first_name', 'last_name', 'street', 'house_number', 'postal_code', 'city', 'email'])
        );
    }
}
