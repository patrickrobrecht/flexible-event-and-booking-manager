<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Models\StorageLocation;
use App\Models\User;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class StorageLocationPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewStorageLocations);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, StorageLocation $storageLocation): Response
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $this->requireAbility($user, Ability::CreateStorageLocations);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, StorageLocation $storageLocation): Response
    {
        return $this->requireAbility($user, Ability::EditStorageLocations);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, StorageLocation $storageLocation): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, StorageLocation $storageLocation): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, StorageLocation $storageLocation): Response
    {
        $childStorageLocationsCount = $storageLocation->getChildStorageLocationsCount();
        if ($childStorageLocationsCount >= 1) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because the storage location has :count child storage locations.', $childStorageLocationsCount, [
                    'name' => $storageLocation->name,
                ])
            );
        }

        $materialsCount = $storageLocation->getMaterialsCount();
        if ($materialsCount >= 1) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because the storage location contains :count materials.', $materialsCount, [
                    'name' => $storageLocation->name,
                ])
            );
        }

        return $this->requireAbility($user, Ability::DestroyStorageLocations);
    }
}
