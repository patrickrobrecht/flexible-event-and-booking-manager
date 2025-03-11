<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Models\User;
use App\Models\UserRole;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class UserRolePolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewUserRoles);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UserRole $userRole): Response
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $this->requireAbility($user, Ability::CreateUserRoles);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UserRole $userRole): Response
    {
        return $this->requireAbility($user, Ability::EditUserRoles);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UserRole $userRole): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UserRole $userRole): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UserRole $userRole): Response
    {
        return $this->deny();
    }
}
