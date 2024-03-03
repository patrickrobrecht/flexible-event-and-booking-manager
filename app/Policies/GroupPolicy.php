<?php

namespace App\Policies;

use App\Models\Event;
use App\Models\Group;
use App\Models\User;
use App\Options\Ability;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class GroupPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user, Event $event): Response
    {
        return $this->requireAbility($user, Ability::ViewBookingsOfEvent);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Group $group): Response
    {
        return $this->requireAbility($user, Ability::ViewBookingsOfEvent);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        if (!$user->can('create', Event::class)) {
            return $this->deny();
        }

        return $this->requireAbility($user, Ability::ManageGroupsOfEvent);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Group $group): Response
    {
        if (!$user->can('update', $group->event)) {
            return $this->deny();
        }

        return $this->create($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Group $group): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Group $group): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Group $group): Response
    {
        if ($group->bookings->isNotEmpty()) {
            return $this->deny();
        }

        return $this->update($user, $group);
    }
}
