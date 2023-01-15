<?php

namespace App\Policies;

use App\Models\Form;
use App\Models\User;
use App\Options\Ability;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class FormPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     *
     * @param User $user
     *
     * @return Response
     */
    public function viewAny(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewForms);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Form $form
     *
     * @return Response
     */
    public function view(User $user, Form $form): Response
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User  $user
     *
     * @return Response
     */
    public function create(User $user): Response
    {
        return $this->requireAbility($user, Ability::CreateForms);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param Form $form
     *
     * @return Response
     */
    public function update(User $user, Form $form): Response
    {
        return $this->requireAbility($user, Ability::EditForms);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Form $form
     *
     * @return Response
     */
    public function delete(User $user, Form $form): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Form $form
     *
     * @return Response
     */
    public function restore(User $user, Form $form): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Form $form
     *
     * @return Response
     */
    public function forceDelete(User $user, Form $form): Response
    {
        return $this->deny();
    }
}
