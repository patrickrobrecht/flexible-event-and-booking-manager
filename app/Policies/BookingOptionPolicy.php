<?php

namespace App\Policies;

use App\Models\BookingOption;
use App\Models\User;
use App\Options\Ability;
use App\Options\BookingRestriction;
use App\Options\Visibility;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class BookingOptionPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, BookingOption $bookingOption): Response
    {
        if ($bookingOption->event->visibility === Visibility::Public) {
            return $this->allow();
        }

        return $this->response(isset($user) && $user->can('view', $bookingOption->event));
    }

    public function book(?User $user, BookingOption $bookingOption): Response
    {
        if (
            !isset($bookingOption->available_from)
            || $bookingOption->available_from->isFuture()
        ) {
            if (isset($bookingOption->available_from)) {
                $message = ' ' . __('The booking period starts at :date.', ['date' => formatDateTime($bookingOption->available_from)]);
            }

            return $this->deny(__('Bookings are not possible yet.') . ($message ?? ''));
        }

        if (isset($bookingOption->available_until) && $bookingOption->available_until->isPast()) {
            return $this->deny(
                __('The booking period ended at :date.', ['date' => formatDateTime($bookingOption->available_until)])
                . ' '
                . __('Bookings are not possible anymore.')
            );
        }

        if ($bookingOption->hasReachedMaximumBookings()) {
            return $this->deny(
                __('The maximum number of bookings has been reached.')
                . ' '
                . __('Bookings are not possible anymore.')
            );
        }

        if (
            !isset($user)
            && $bookingOption->isRestrictedBy(BookingRestriction::AccountRequired)
        ) {
            return $this->deny(__('Bookings are only available for logged-in users.'));
        }

        if (
            (!isset($user) || $user->email_verified_at === null)
            && $bookingOption->isRestrictedBy(BookingRestriction::VerifiedEmailAddressRequired)
        ) {
            return $this->deny(__('Bookings are only available for logged-in users with a verified email address.'));
        }

        return $this->allow();
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): Response
    {
        return $this->requireAbility($user, Ability::ManageBookingOptionsOfEvent);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, BookingOption $bookingOption): Response
    {
        return $this->create($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, BookingOption $bookingOption): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, BookingOption $bookingOption): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, BookingOption $bookingOption): Response
    {
        return $this->deny();
    }
}
