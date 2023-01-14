<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\User;
use App\Options\Ability;
use App\Options\Visibility;
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
     * @param Event $event
     *
     * @return Response
     */
    public function view(?User $user, Event $event): Response
    {
        if ($event->visibility === Visibility::Public) {
            // Anyone can view public events.
            return $this->allow();
        }

        // Private events are only visible for logged-in users with the ability to view private events as well.
        return $this->requireAbility($user, Ability::ViewPrivateEvents);
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

    public function createChild(User $user, Event $event): Response
    {
        return $this->response(
            $event->parent_event_id === null
            && $this->create($user)->allowed()
        );
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param Event $event
     *
     * @return Response
     */
    public function update(User $user, Event $event): Response
    {
        return $this->requireAbility($user, Ability::EditEvents);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param Event $event
     *
     * @return Response
     */
    public function delete(User $user, Event $event): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param Event $event
     *
     * @return Response
     */
    public function restore(User $user, Event $event): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param Event $event
     *
     * @return Response
     */
    public function forceDelete(User $user, Event $event): Response
    {
        return $this->deny();
    }
}
