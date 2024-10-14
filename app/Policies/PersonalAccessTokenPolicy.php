<?php

namespace App\Policies;

use App\Models\User;
use App\Options\Ability;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;
use Laravel\Sanctum\PersonalAccessToken;

class PersonalAccessTokenPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        // Nobody may see personal access tokens of other users.
        return $this->deny();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function viewOwn(User $user): Response
    {
        return $this->requireAbility($user, Ability::ManagePersonalAccessTokens);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PersonalAccessToken $personalAccessToken): Response
    {
        return $this->update($user, $personalAccessToken);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $this->requireAbility($user, Ability::ManagePersonalAccessTokens);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PersonalAccessToken $personalAccessToken): Response
    {
        return $this->response(
            $user->is($personalAccessToken->tokenable)
            && $user->hasAbility(Ability::ManagePersonalAccessTokens)
        );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PersonalAccessToken $personalAccessToken): Response
    {
        return $this->update($user, $personalAccessToken);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PersonalAccessToken $personalAccessToken): Response
    {
        return $this->requireAbility($user, Ability::ManagePersonalAccessTokens);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PersonalAccessToken $personalAccessToken): Response
    {
        return $this->update($user, $personalAccessToken);
    }
}
