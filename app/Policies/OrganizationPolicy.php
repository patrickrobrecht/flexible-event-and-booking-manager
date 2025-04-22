<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Models\Organization;
use App\Models\User;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class OrganizationPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewOrganizations);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Organization $organization): Response
    {
        return $this->viewAny($user);
    }

    public function viewResponsibilities(?User $user, Organization $organization): Response
    {
        if (!isset($user)) {
            return $this->deny();
        }
        $viewResponse = $this->view($user, $organization);
        if ($viewResponse->denied()) {
            return $viewResponse;
        }

        return $this->requireAbilityOrCheck(
            $user,
            Ability::ViewResponsibilitiesOfOrganizations,
            fn () => $this->response(
                $organization->hasPubliclyVisibleResponsibleUsers()
                || $user->isResponsibleFor($organization)
            )
        );
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $this->requireAbility($user, Ability::CreateOrganizations);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Organization $organization): Response
    {
        return $this->requireAbility($user, Ability::EditOrganizations);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Organization $organization): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Organization $organization): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Organization $organization): Response
    {
        $eventsCount = $organization->events_count ?? $organization->events()->count();
        if ($eventsCount >= 1) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because the organization is referenced by :count events.', $eventsCount, [
                    'name' => $organization->name,
                ])
            );
        }

        $eventsSeriesCount = $organization->events_series_count ?? $organization->eventSeries()->count();
        if ($eventsSeriesCount >= 1) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because the organization is referenced by :count event series.', $eventsSeriesCount, [
                    'name' => $organization->name,
                ])
            );
        }

        return $this->requireAbility($user, Ability::DestroyOrganizations);
    }
}
