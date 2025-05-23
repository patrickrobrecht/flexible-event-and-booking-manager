<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Enums\Visibility;
use App\Models\EventSeries;
use App\Models\User;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class EventSeriesPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewEventSeries);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, EventSeries $eventSeries): Response
    {
        if ($eventSeries->visibility === Visibility::Public) {
            // Anyone can view public event series.
            return $this->allow();
        }

        /**
         * Private event series are only visible for
         * - logged-in users with the ability to view private event series as well
         * - and the responsible users.
         */
        return $this->requireAbilityOrCheck(
            $user,
            Ability::ViewPrivateEventSeries,
            fn () => $this->response(isset($user) && $user->isResponsibleFor($eventSeries))
        );
    }

    public function viewResponsibilities(?User $user, EventSeries $eventSeries): Response
    {
        $viewResponse = $this->view($user, $eventSeries);
        if ($viewResponse->denied()) {
            return $viewResponse;
        }

        return $this->requireAbilityOrCheck(
            $user,
            Ability::ViewResponsibilitiesOfEventSeries,
            fn () => $this->response(
                $eventSeries->hasPubliclyVisibleResponsibleUsers()
                || (isset($user) && $user->isResponsibleFor($eventSeries))
            )
        );
    }

    /**
     * Determine whether the user can create models.
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
     */
    public function update(User $user, EventSeries $eventSeries): Response
    {
        return $this->requireAbility($user, Ability::EditEventSeries);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, EventSeries $eventSeries): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, EventSeries $eventSeries): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, EventSeries $eventSeries): Response
    {
        $eventsCount = $eventSeries->events_count ?? $eventSeries->events()->count();
        if ($eventsCount >= 1) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because the event series is referenced by :count events.', $eventsCount, [
                    'name' => $eventSeries->name,
                ])
            );
        }

        $subEventSeriesCount = $eventSeries->sub_event_series_count ?? $eventSeries->subEventSeries()->count();
        if ($subEventSeriesCount >= 1) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because the event series has :count sub events series.', $subEventSeriesCount, [
                    'name' => $eventSeries->name,
                ])
            );
        }

        return $this->requireAbility($user, Ability::DestroyEventSeries);
    }
}
