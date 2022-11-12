<?php

namespace App\Policies;

use App\Models\User;
use App\Options\Ability;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class UserPolicy
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
        return $this->requireAbility($user, Ability::ViewUsers);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param User $model
     * @return Response
     */
    public function view(User $user, User $model): Response
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     * @return Response
     */
    public function create(User $user): Response
    {
        return $this->requireAbility($user, Ability::CreateUsers);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param User $model
     * @return Response
     */
    public function update(User $user, User $model): Response
    {
        return $this->requireAbility($user, Ability::EditUsers);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param User $model
     * @return Response
     */
    public function delete(User $user, User $model): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param User $model
     * @return Response
     */
    public function restore(User $user, User $model): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param User $model
     * @return Response
     */
    public function forceDelete(User $user, User $model): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether a user can edit his/her profile.
     *
     * @param User $user
     * @return Response
     */
    public function editAccount(User $user): Response
    {
        return $this->requireAbility($user, Ability::EditAccount);
    }

    /**
     * Determine whether a user can register.
     *
     * @param ?User $user
     * @return Response
     */
    public function register(?User $user): Response
    {
        return $this->response(config('app.features.registration'));
    }
}
