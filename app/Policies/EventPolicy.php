<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Enums\Visibility;
use App\Models\Event;
use App\Models\User;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class EventPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewEvents);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Event $event): Response
    {
        if ($event->visibility === Visibility::Public) {
            // Anyone can view public events.
            return $this->allow();
        }

        /**
         * Private events are only visible for
         * - logged-in users with the ability to view private events as well
         * - and the responsible users.
         */
        return $this->requireAbilityOrCheck(
            $user,
            Ability::ViewPrivateEvents,
            fn () => $this->response(isset($user) && $user->isResponsibleFor($event))
        );
    }

    public function viewGroups(User $user, Event $event): Response
    {
        if ($event->getBookingOptions()->isEmpty()) {
            return $this->deny();
        }

        return $this->requireAbility($user, Ability::ViewBookingsOfEvent);
    }

    public function viewResponsibilities(?User $user, Event $event): Response
    {
        $viewResponse = $this->view($user, $event);
        if ($viewResponse->denied()) {
            return $viewResponse;
        }

        return $this->requireAbilityOrCheck(
            $user,
            Ability::ViewResponsibilitiesOfEvents,
            fn () => $this->response(
                $event->hasPubliclyVisibleResponsibleUsers()
                || (isset($user) && $user->isResponsibleFor($event))
            )
        );
    }

    /**
     * Determine whether the user can create models.
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
     */
    public function update(User $user, Event $event): Response
    {
        return $this->requireAbility($user, Ability::EditEvents);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Event $event): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Event $event): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Event $event): Response
    {
        $subEventsCount = $event->sub_events_count ?? $event->subEvents()->count();
        if ($subEventsCount >= 1) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because the event has :count sub events.', $subEventsCount, [
                    'name' => $event->name,
                ])
            );
        }

        $bookingOptionsCount = $event->booking_options_count ?? $event->bookingOptions()->count();
        if ($bookingOptionsCount >= 1) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because the event has :count booking options.', $bookingOptionsCount, [
                    'name' => $event->name,
                ])
            );
        }

        return $this->requireAbility($user, Ability::DestroyEvents);
    }

    public function exportGroups(User $user): Response
    {
        return $this->requireAbility($user, Ability::ExportGroupsOfEvent);
    }
}
