<?php

namespace App\Policies;

use App\Models\EventSeries;
use App\Models\User;
use App\Options\Ability;
use App\Options\Visibility;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class EventSeriesPolicy
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
        return $this->requireAbility($user, Ability::ViewEventSeries);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param EventSeries $eventSeries
     *
     * @return Response
     */
    public function view(User $user, EventSeries $eventSeries): Response
    {
        if ($eventSeries->visibility === Visibility::Public) {
            // Anyone can view public event series.
            return $this->allow();
        }

        /**
         * Private event series are only visible for logged-in users
         * with the ability to view private event series as well.
         */
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
        return $this->requireAbility($user, Ability::CreateEventSeries);
    }

    public function createChild(User $user, EventSeries $eventSeries): Response
    {
        return $this->response(
            $eventSeries->parent_event_series_id === null
            && $this->create($user)->allowed()
        );
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param  User  $user
     * @param EventSeries $eventSeries
     *
     * @return Response
     */
    public function update(User $user, EventSeries $eventSeries): Response
    {
        return $this->requireAbility($user, Ability::EditEventSeries);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param  User  $user
     * @param EventSeries $eventSeries
     *
     * @return Response
     */
    public function delete(User $user, EventSeries $eventSeries): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param  User  $user
     * @param EventSeries $eventSeries
     *
     * @return Response
     */
    public function restore(User $user, EventSeries $eventSeries): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param  User  $user
     * @param EventSeries $eventSeries
     *
     * @return Response
     */
    public function forceDelete(User $user, EventSeries $eventSeries): Response
    {
        return $this->deny();
    }
}
