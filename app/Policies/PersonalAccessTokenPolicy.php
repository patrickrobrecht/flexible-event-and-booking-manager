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
     *
     * @param User $user
     * @return Response
     */
    public function viewAny(User $user): Response
    {
        // Nobody may see personal access tokens of other users.
        return $this->deny();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @return Response
     */
    public function viewOwn(User $user): Response
    {
        return $this->requireAbility($user, Ability::ManagePersonalAccessTokens);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param PersonalAccessToken $personalAccessToken
     * @return Response
     */
    public function view(User $user, PersonalAccessToken $personalAccessToken): Response
    {
        return $this->response(
            $user->is($personalAccessToken->tokenable)
            && $user->hasAbility(Ability::ManagePersonalAccessTokens)
        );
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response
     */
    public function create(User $user): Response
    {
        return $this->requireAbility($user, Ability::ManagePersonalAccessTokens);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param PersonalAccessToken $personalAccessToken
     * @return Response
     */
    public function update(User $user, PersonalAccessToken $personalAccessToken): Response
    {
        return $this->requireAbility($user, Ability::ManagePersonalAccessTokens);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param PersonalAccessToken $personalAccessToken
     * @return Response
     */
    public function delete(User $user, PersonalAccessToken $personalAccessToken): Response
    {
        return $this->requireAbility($user, Ability::ManagePersonalAccessTokens);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param PersonalAccessToken $personalAccessToken
     * @return Response
     */
    public function restore(User $user, PersonalAccessToken $personalAccessToken): Response
    {
        return $this->requireAbility($user, Ability::ManagePersonalAccessTokens);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param PersonalAccessToken $personalAccessToken
     * @return Response
     */
    public function forceDelete(User $user, PersonalAccessToken $personalAccessToken): Response
    {
        return $this->requireAbility($user, Ability::ManagePersonalAccessTokens);
    }
}
