<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\PersonalAccessTokenController;
use App\Http\Requests\PersonalAccessTokenRequest;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Options\Ability;
use App\Policies\PersonalAccessTokenPolicy;
use Database\Factories\PersonalAccessTokenFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Ability::class)]
#[CoversClass(PersonalAccessToken::class)]
#[CoversClass(PersonalAccessTokenController::class)]
#[CoversClass(PersonalAccessTokenFactory::class)]
#[CoversClass(PersonalAccessTokenPolicy::class)]
#[CoversClass(PersonalAccessTokenRequest::class)]
class PersonalAccessTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testOwnPersonalAccessTokensCanBeListedWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/personal-access-tokens', Ability::ManagePersonalAccessTokens);
    }

    public function testCreatePersonalAccessTokenFormIsOnlyAccessibleWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/personal-access-tokens/create', Ability::ManagePersonalAccessTokens);
    }

    public function testEditPersonalAccessTokenFormIsAccessibleOnlyForOwnTokensWithCorrectAbility(): void
    {
        $token = PersonalAccessToken::factory()
            ->for(User::factory()->create(), 'tokenable')
            ->create();
        $userOwningTheToken = $token->tokenable;

        $editRoute = "/personal-access-tokens/{$token->id}/edit";
        $this->assertGuestCannotGet($editRoute, true);

        // Another user cannot update the token.
        $userRole = $this->createUserRoleWithAbility(Ability::ManagePersonalAccessTokens);
        $anotherUser = User::factory()
            ->hasAttached($userRole)
            ->create();
        $this->actingAs($anotherUser);
        $this->get($editRoute)->assertForbidden();

        // User without the ability cannot update own token.
        $this->actingAs($userOwningTheToken);
        $this->get($editRoute)->assertForbidden();

        // User with the ability can update own token.
        $userOwningTheToken->userRoles()->sync([$userRole->id]);
        $userOwningTheToken->refresh(); // Load new user roles!
        $this->get($editRoute)->assertOk();
    }

    public function testDeletingPersonalAccessTokensIsOnlyAllowedForOwnTokensWithCorrectAbility(): void
    {
        $token = PersonalAccessToken::factory()
            ->for(User::factory()->create(), 'tokenable')
            ->create();
        $userOwningTheToken = $token->tokenable;

        $deleteRoute = "/personal-access-tokens/{$token->id}";

        // Another user cannot update the token.
        $userRole = $this->createUserRoleWithAbility(Ability::ManagePersonalAccessTokens);
        $anotherUser = User::factory()
            ->hasAttached($userRole)
            ->create();
        $this->actingAs($anotherUser);
        $this->delete($deleteRoute)->assertForbidden();

        // User without the ability cannot update own token.
        $this->actingAs($userOwningTheToken);
        $this->delete($deleteRoute)->assertForbidden();

        // User with the ability can update own token.
        $userOwningTheToken->userRoles()->sync([$userRole->id]);
        $userOwningTheToken->refresh(); // Load new user roles!
        $this->delete($deleteRoute)->assertRedirect()->assertRedirectToRoute('personal-access-tokens.index');
    }
}
