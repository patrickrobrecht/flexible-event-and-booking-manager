<?php

namespace App\Policies\Traits;

use App\Models\User;
use App\Options\Ability;
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
}
