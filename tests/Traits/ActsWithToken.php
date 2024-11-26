<?php

namespace Tests\Traits;

use App\Models\PersonalAccessToken;
use App\Models\User;
use App\Options\Ability;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\NewAccessToken;

trait ActsWithToken
{
    /**
     * @param  Ability|Ability[]  $ability
     */
    protected function assertTokenCanGetOnlyWithAbility(string $route, Ability|array $ability): void
    {
        $this->assertTokenCannotGetWithoutAbility($route, $ability);
        $this->assertTokenCanGetWithAbility($route, $ability);
    }

    /**
     * @param  Ability|Ability[]  $ability
     */
    protected function assertTokenCanGetWithAbility(string $route, Ability|array $ability): TestResponse
    {
        return $this->withHeadersForApiRequestWithAbility($ability)
            ->get($route)
            ->assertOk();
    }

    /**
     * @param  Ability|Ability[]  $ability
     */
    protected function assertTokenCannotGetWithoutAbility(string $route, Ability|array $ability): TestResponse
    {
        return $this->withHeadersForApiRequestWithAbility(Ability::casesExcept($ability))
            ->get($route)
            ->assertForbidden();
    }

    /**
     * @param  Ability|Ability[]  $ability
     */
    protected function createTokenWithAbility(Ability|array $ability): NewAccessToken
    {
        $user = User::factory()->create();
        return PersonalAccessToken::createTokenFromValidated($user, [
            'name' => 'Test Token',
            'abilities' => is_array($ability) ? $ability : [$ability],
        ]);
    }

    protected function withHeadersForApiRequest(string $tokenString): self
    {
        return $this->withHeaders([
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $tokenString,
        ]);
    }

    /**
     * @param  Ability|Ability[]  $ability
     */
    protected function withHeadersForApiRequestWithAbility(Ability|array $ability): self
    {
        $token = $this->createTokenWithAbility($ability);

        return $this->withHeadersForApiRequest($token->plainTextToken);
    }
}
