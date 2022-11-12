<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserRole;
use App\Options\Ability;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class UserRolePolicy
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
        return $this->requireAbility($user, Ability::ViewUserRoles);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param UserRole $userRole
     * @return Response
     */
    public function view(User $user, UserRole $userRole): Response
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
        return $this->requireAbility($user, Ability::CreateUserRoles);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param UserRole $userRole
     * @return Response
     */
    public function update(User $user, UserRole $userRole): Response
    {
        return $this->requireAbility($user, Ability::EditUserRoles);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param UserRole $userRole
     * @return Response
     */
    public function delete(User $user, UserRole $userRole): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param UserRole $userRole
     * @return Response
     */
    public function restore(User $user, UserRole $userRole): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param UserRole $userRole
     * @return Response
     */
    public function forceDelete(User $user, UserRole $userRole): Response
    {
        return $this->deny();
    }
}
