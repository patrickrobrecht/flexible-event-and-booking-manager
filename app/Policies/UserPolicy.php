<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Models\User;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewUsers);
    }

    /**
     * Determine whether the user can view the user's profile.
     */
    public function view(User $user, User $model): Response
    {
        /**
         * {@see self::viewAccount()} for own profile.
         */

        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $this->requireAbility($user, Ability::CreateUsers);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): Response
    {
        return $this->requireAbility($user, Ability::EditUsers);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): Response
    {
        if ($model->is($user)) {
            return $this->deny(
                __(':name cannot be deleted because it is your own account.', ['name' => $user->name])
            );
        }

        $bookingsCount = ($model->bookings_count ?? $model->bookings()->count())
            + ($model->bookings_trashed_count ?? $model->bookingsTrashed()->count());
        if ($bookingsCount > 0) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because (s)he has :count bookings.', $bookingsCount, [
                    'name' => $model->name,
                ])
            );
        }

        $documentCount = $model->documents_count ?? $model->documents()->count();
        if ($documentCount > 0) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because (s)he uploaded :count documents.', $documentCount, [
                    'name' => $model->name,
                ])
            );
        }

        $responsibleForEventsCount = $model->responsible_for_events_count ?? $model->responsibleForEvents()->count();
        if ($responsibleForEventsCount > 0) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because (s)he is responsible for :count events.', $responsibleForEventsCount, [
                    'name' => $model->name,
                ])
            );
        }

        $responsibleForEventSeriesCount = $model->responsible_for_event_series_count ?? $model->responsibleForEventSeries()->count();
        if ($responsibleForEventSeriesCount > 0) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because (s)he is responsible for :count event series.', $responsibleForEventSeriesCount, [
                    'name' => $model->name,
                ])
            );
        }

        $responsibleForOrganizationsCount = $model->responsible_for_organizations_count ?? $model->responsibleForOrganizations()->count();
        if ($responsibleForOrganizationsCount > 0) {
            return $this->deny(
                formatTransChoice(':name cannot be deleted because (s)he is responsible for :count organizations.', $responsibleForOrganizationsCount, [
                    'name' => $model->name,
                ])
            );
        }

        return $this->requireAbility($user, Ability::DestroyUsers);
    }

    /**
     * Determine whether a user can view his/her profile.
     */
    public function viewAccount(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewAccount);
    }

    public function viewAbilities(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewAbilities);
    }

    /**
     * Determine whether a user can edit his/her profile.
     */
    public function editAccount(User $user): Response
    {
        return $this->requireAbility($user, Ability::EditAccount);
    }

    /**
     * Determine whether a user can register.
     */
    public function register(?User $user): Response
    {
        return $this->response(config('app.features.registration'));
    }

    public function viewSystemInformation(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewSystemInformation);
    }
}
