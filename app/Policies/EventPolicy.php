<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use App\Options\Ability;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class EventPolicy
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
        return $this->requireAbility($user, Ability::ViewEvents);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Event $Event
     *
     * @return Response
     */
    public function view(User $user, Event $Event): Response
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
        return $this->requireAbility($user, Ability::CreateEvents);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param Event $Event
     *
     * @return Response
     */
    public function update(User $user, Event $Event): Response
    {
        return $this->requireAbility($user, Ability::EditEvents);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param Event $Event
     *
     * @return Response
     */
    public function delete(User $user, Event $Event): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param Event $Event
     *
     * @return Response
     */
    public function restore(User $user, Event $Event): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param Event $Event
     *
     * @return Response
     */
    public function forceDelete(User $user, Event $Event): Response
    {
        return $this->deny();
    }
}
