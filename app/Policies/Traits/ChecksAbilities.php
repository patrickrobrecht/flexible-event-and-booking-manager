<?php

namespace App\Policies\Traits;

use App\Enums\Ability;
use App\Models\User;
use Closure;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

trait ChecksAbilities
{
    use HandlesAuthorization;

    public function response(bool $allowed): Response
    {
        return $allowed ? $this->allow() : $this->deny();
    }

    /**
     * @param Ability[] $abilities
     */
    public function requireAbilities(?User $user, array $abilities): Response
    {
        if (!isset($user)) {
            return $this->deny();
        }

        if (array_any($abilities, fn ($ability) => !$user->hasAbility($ability))) {
            return $this->deny();
        }

        return $this->allow();
    }

    public function requireAbility(?User $user, Ability $ability): Response
    {
        if (!isset($user)) {
            return $this->deny();
        }

        return $this->response($user->hasAbility($ability));
    }

    /**
     * @param Ability[] $abilities
     */
    public function requireOneAbilityOf(?User $user, array $abilities): Response
    {
        if (!isset($user)) {
            return $this->deny();
        }

        if (array_any($abilities, fn ($ability) => $user->hasAbility($ability))) {
            return $this->allow();
        }

        return $this->deny();
    }

    /**
     * @param Closure(): Response $closure
     */
    public function requireAbilityOrCheck(?User $user, Ability $ability, Closure $closure): Response
    {
        $abilityResponse = $this->requireAbility($user, $ability);

        return $abilityResponse->allowed()
            ? $abilityResponse
            : $closure();
    }
}
