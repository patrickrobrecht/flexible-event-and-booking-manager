<?php

namespace Tests\Traits;

use App\Enums\Ability;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Testing\TestResponse;
use Laravel\Sanctum\NewAccessToken;
use Symfony\Component\HttpFoundation\Response;

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
    protected function assertTokenCannotGetDespiteAbility(string $route, Ability|array $ability, int $statusCode = Response::HTTP_FORBIDDEN): TestResponse
    {
        return $this->withHeadersForApiRequestWithAbility($ability)
            ->get($route)
            ->assertStatus($statusCode);
    }

    /**
     * @param  Ability|Ability[]  $ability
     */
    protected function assertTokenCannotGetWithoutAbility(string $route, Ability|array $ability): TestResponse
    {
        return $this->withHeadersForApiRequestWithAbility(Ability::apiCasesExcept($ability))
            ->get($route)
            ->assertForbidden();
    }

    /**
     * @param  Ability|Ability[]  $ability
     */
    protected function createTokenWithAbility(Ability|array $ability): NewAccessToken
    {
        $user = User::factory()->create();
        $name = is_array($ability)
            ? implode(', ', array_map(static fn ($case) => $case->value, $ability))
            : $ability->value;

        return PersonalAccessToken::createTokenFromValidated($user, [
            'name' => 'Test Token with ' . $name,
            'abilities' => is_array($ability) ? $ability : [$ability],
        ]);
    }

    protected function withHeadersForApiRequest(string $tokenString): self
    {
        Auth::forgetGuards();
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
