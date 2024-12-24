<?php

namespace Tests\Feature\Http\Api;

use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Options\Ability;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;

class AccountApiTest extends TestCase
{
    use ActsWithToken;
    use RefreshDatabase;

    public function testAccountDataCanBeRequestedWithCorrectAbility(): void
    {
        $this->assertTokenCanGetWithAbility('api/account', Ability::ViewAccount);
    }

    public function testAccountDataCannotBeRequestedWithoutCorrectAbility(): void
    {
        $this->assertTokenCannotGetWithoutAbility('api/account', Ability::ViewAccount);
    }

    public function testAccountDataCannotBeRequestedWithInvalidToken(): void
    {
        $token = $this->createTokenWithAbility(Ability::ViewAccount);

        $this->withHeadersForApiRequest($token->accessToken->id.'|invalidTokenString')
            ->get('api/account')
            ->assertStatus(401);
    }

    public function testAccountDataCannotBeRequestedWithExpiredToken(): void
    {
        $user = User::factory()->create();
        $token = PersonalAccessToken::createTokenFromValidated($user, [
            'name' => 'Test Token',
            'abilities' => [Ability::ViewAccount],
            'expires_at' => Carbon::yesterday()->format('Y-m-d\TH:i'),
        ]);

        $this->withHeadersForApiRequest($token->plainTextToken)
            ->get('api/account')
            ->assertStatus(401);
    }
}
