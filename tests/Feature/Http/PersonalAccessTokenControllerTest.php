<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\PersonalAccessTokenController;
use App\Http\Requests\PersonalAccessTokenRequest;
use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Options\Ability;
use App\Policies\PersonalAccessTokenPolicy;
use App\Providers\AppServiceProvider;
use Database\Factories\PersonalAccessTokenFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(Ability::class)]
#[CoversClass(AppServiceProvider::class)]
#[CoversClass(PersonalAccessToken::class)]
#[CoversClass(PersonalAccessTokenController::class)]
#[CoversClass(PersonalAccessTokenFactory::class)]
#[CoversClass(PersonalAccessTokenPolicy::class)]
#[CoversClass(PersonalAccessTokenRequest::class)]
class PersonalAccessTokenControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewOwnPersonalAccessTokensOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/personal-access-tokens', Ability::ManagePersonalAccessTokens);
    }

    public function testUserCanOpenCreatePersonalAccessTokenFormOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('/personal-access-tokens/create', Ability::ManagePersonalAccessTokens);
    }

    public function testUserCanStorePersonalAccessTokenOnlyWithCorrectAbility(): void
    {
        $data = PersonalAccessToken::factory()->makeOne()->toArray();

        $this->assertUserCanPostOnlyWithAbility('personal-access-tokens', $data, Ability::ManagePersonalAccessTokens, null);
    }

    public function testUserCanOpenEditFormOnlyForOwnPersonalAccessTokensAndWithCorrectAbility(): void
    {
        $token = self::createToken();
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

    public function testUserCanUpdateOnlyOwnPersonalAccessTokensAndWithCorrectAbility(): void
    {
        $token = self::createToken();
        $userOwningTheToken = $token->tokenable;

        $fromEditForm = $this->from("personal-access-tokens/{$token->id}/edit");
        $updateRoute = "/personal-access-tokens/{$token->id}";
        $updateData = array_intersect_key(PersonalAccessToken::factory()->makeOne()->toArray(), array_flip(['name', 'abilities']));

        // Another user cannot update the token.
        $userRole = $this->createUserRoleWithAbility(Ability::ManagePersonalAccessTokens);
        $anotherUser = User::factory()
            ->hasAttached($userRole)
            ->create();
        $this->actingAs($anotherUser);
        $fromEditForm->put($updateRoute, $updateData)->assertForbidden();

        // User without the ability cannot delete own token.
        $this->actingAs($userOwningTheToken);
        $fromEditForm->put($updateRoute, $updateData)->assertForbidden();

        // User with the ability can delete own token.
        $userOwningTheToken->userRoles()->sync([$userRole->id]);
        $userOwningTheToken->refresh(); // Load new user roles!
        $fromEditForm->put($updateRoute, $updateData)->assertRedirect("personal-access-tokens/{$token->id}/edit");
    }

    public function testUserCanDeleteOnlyOwnPersonalAccessTokensAndWithCorrectAbility(): void
    {
        $token = self::createToken();
        $userOwningTheToken = $token->tokenable;

        $deleteRoute = "/personal-access-tokens/{$token->id}";

        // Another user cannot delete the token.
        $userRole = $this->createUserRoleWithAbility(Ability::ManagePersonalAccessTokens);
        $anotherUser = User::factory()
            ->hasAttached($userRole)
            ->create();
        $this->actingAs($anotherUser);
        $this->delete($deleteRoute)->assertForbidden();

        // User without the ability cannot delete own token.
        $this->actingAs($userOwningTheToken);
        $this->delete($deleteRoute)->assertForbidden();

        // User with the ability can delete own token.
        $userOwningTheToken->userRoles()->sync([$userRole->id]);
        $userOwningTheToken->refresh(); // Load new user roles!
        $this->delete($deleteRoute)->assertRedirectToRoute('personal-access-tokens.index');
    }

    private static function createToken(): PersonalAccessToken
    {
        return PersonalAccessToken::factory()
            ->for(User::factory()->create(), 'tokenable')
            ->create();
    }
}
