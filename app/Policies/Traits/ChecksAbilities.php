<?php

namespace App\Policies\Traits;

use App\Models\User;
use App\Options\Ability;
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

    public function requireAbility(?User $user, Ability $ability): Response
    {
        if (!isset($user)) {
            return $this->deny();
        }

        return $this->response($user->hasAbility($ability));
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
