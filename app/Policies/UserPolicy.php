<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Models\User;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewUsers);
    }

    /**
     * Determine whether the user can view the user's profile.
     */
    public function view(User $user, User $model): Response
    {
        /**
         * {@see self::viewAccount()} for own profile.
         */

        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $this->requireAbility($user, Ability::CreateUsers);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {
        return $this->requireAbility($user, Ability::EditUsers);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether a user can view his/her profile.
     */
    public function viewAccount(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewAccount);
    }

    public function viewAbilities(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewAbilities);
    }

    /**
     * Determine whether a user can edit his/her profile.
     */
    public function editAccount(User $user): Response
    {
        return $this->requireAbility($user, Ability::EditAccount);
    }

    /**
     * Determine whether a user can register.
     */
    public function register(?User $user): Response
    {
        return $this->response(config('app.features.registration'));
    }
}
