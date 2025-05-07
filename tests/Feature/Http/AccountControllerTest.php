<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Http\Controllers\AccountController;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Policies\UserPolicy;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(AccountController::class)]
#[CoversClass(User::class)]
#[CoversClass(UserPolicy::class)]
#[CoversClass(UserRequest::class)]
class AccountControllerTest extends TestCase
{
    public function testUserCanViewAccountOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/account', [Ability::ViewAccount, Ability::ViewAbilities]);
    }

    public function testUserCanViewAbilitiesOnlyWithCorrectAbility(): void
    {
        $this->actingAsUserWithAbility(Ability::ViewAccount);
        $this->get('/account')->assertDontSee(__('Abilities'));

        $this->actingAsUserWithAbility([Ability::ViewAccount, Ability::ViewAbilities]);
        $this->get('/account')->assertSee(__('Abilities'));
    }

    public function testUserCanOpenEditAccountFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/account/edit', Ability::EditAccount);
    }

    public function testUserCanUpdateAccountWithCorrectAbility(): void
    {
        $user = $this->actingAsUserWithAbility(Ability::EditAccount);

        $this->put('/account', $this->getRandomUserData($user))
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect('/account/edit');
    }

    public function testUserCannotUpdateAccountWithoutAbility(): void
    {
        $user = $this->actingAsUserWithAbility(Ability::ViewAccount);

        $this->put('/account', $this->getRandomUserData($user))->assertForbidden();
    }

    public function testUserReceivesErrorMessagesForInvalidAccountData(): void
    {
        $user = $this->actingAsUserWithAbility(Ability::EditAccount);

        $data = array_replace($this->getRandomUserData($user), ['first_name' => null]);
        $this->put('/account', $data)
            ->assertSessionHasErrors([
                'first_name' => 'Vorname muss ausgefüllt werden.',
            ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function getRandomUserData(User $user): array
    {
        $userData = User::factory()->makeOne();

        return [
            'first_name' => $userData->first_name,
            'last_name' => $userData->last_name,
            'email' => $user->email,
        ];
    }
}
