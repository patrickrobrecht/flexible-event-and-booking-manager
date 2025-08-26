<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Models\Location;
use App\Models\User;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class LocationPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewLocations);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Location $location): Response
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $this->requireAbility($user, Ability::CreateLocations);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Location $location): Response
    {
        return $this->requireAbility($user, Ability::EditLocations);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Location $location): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Location $location): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Location $location): Response
    {
        $eventsCount = $location->events_count ?? $location->events()->count();
        if ($eventsCount >= 1) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because the location is referenced by :count events.', $eventsCount, [
                    'name' => $location->nameOrAddress,
                ])
            );
        }

        $organizationsCount = $location->organizations_count ?? $location->organizations()->count();
        if ($organizationsCount >= 1) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because the location is referenced by :count organizations.', $organizationsCount, [
                    'name' => $location->nameOrAddress,
                ])
            );
        }

        return $this->requireAbility($user, Ability::DestroyLocations);
    }
}
