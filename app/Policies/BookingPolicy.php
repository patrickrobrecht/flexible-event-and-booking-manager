<?php

namespace App\Policies;

use App\Enums\Ability;
use App\Enums\BookingRestriction;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\User;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class BookingPolicy
{
    use ChecksAbilities;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewBookingsOfEvent);
    }

    public function viewAnyPaymentStatus(User $user): Response
    {
        return $this->requireAbility($user, Ability::ViewPaymentStatus);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Booking $booking): Response
    {
        $viewAny = $this->viewAny($user);
        if ($viewAny->allowed()) {
            return $viewAny;
        }

        return $this->viewOnlyOwnBooking($user, $booking);
    }

    public function viewPDF(User $user, Booking $booking): Response
    {
        return $this->view($user, $booking);
    }

    public function viewPaymentStatus(User $user, Booking $booking): Response
    {
        $viewAny = $this->viewAnyPaymentStatus($user);
        if ($viewAny->allowed()) {
            return $viewAny;
        }

        return $this->viewOnlyOwnBooking($user, $booking);
    }

    private function viewOnlyOwnBooking(User $user, Booking $booking): Response
    {
        return $this->response($user->is($booking->bookedByUser));
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(?User $user, BookingOption $bookingOption): Response
    {
        // No start of booking period defined or the start is in the future.
        if (!isset($bookingOption->available_from) || $bookingOption->available_from->isFuture()) {
            if (isset($bookingOption->available_from)) {
                $message = ' ' . __('The booking period starts at :date.', ['date' => formatDateTime($bookingOption->available_from)]);
            }

            return $this->deny(__('Bookings are not possible yet.') . ($message ?? ''));
        }

        // End of the booking period set and it's over.
        if (isset($bookingOption->available_until) && $bookingOption->available_until->isPast()) {
            return $this->deny(
                __('The booking period ended at :date.', ['date' => formatDateTime($bookingOption->available_until)])
                . ' '
                . __('Bookings are not possible anymore.')
            );
        }

        // Guest user, but the booking option requires an account.
        if (!isset($user) && $bookingOption->isRestrictedBy(BookingRestriction::AccountRequired)) {
            return $this->deny(__('Bookings are only available for logged-in users.'));
        }

        // Guest user or user with unverified email address, but the booking option requires a verified address.
        if (
            (!isset($user) || $user->email_verified_at === null)
            && $bookingOption->isRestrictedBy(BookingRestriction::VerifiedEmailAddressRequired)
        ) {
            return $this->deny(__('Bookings are only available for logged-in users with a verified email address.'));
        }

        $allowedBookingStatus = $bookingOption->calculateStatusForNextBooking();
        if ($allowedBookingStatus === null) {
            $message = __('Bookings are not possible anymore.');

            if ($bookingOption->hasReachedMaximumBookings()) {
                $message = __('The maximum number of bookings has already been reached.') . ' ' . $message;
            }

            if ($bookingOption->hasReachedMaximumWaitingListPlaces()) {
                $message = __('All places on the waiting list have already been allocated.') . ' ' . $message;
            }

            return $this->deny($message);
        }

        return $this->allow();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): Response
    {
        return $this->requireAbility($user, Ability::EditBookingsOfEvent);
    }

    public function updateAnyBookingComment(User $user, ?BookingOption $bookingOption = null): Response
    {
        return $this->requireAbility($user, Ability::EditBookingComment);
    }

    public function updateBookingComment(User $user, Booking $booking): Response
    {
        return $this->requireAbility($user, Ability::EditBookingComment);
    }

    public function updateAnyPaymentStatus(User $user, BookingOption $bookingOption): Response
    {
        return $this->requireAbility($user, Ability::EditPaymentStatus);
    }

    public function updatePaymentStatus(User $user, Booking $booking): Response
    {
        return $this->requireAbility($user, Ability::EditPaymentStatus);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Booking $booking): Response
    {
        if ($booking->trashed()) {
            return $this->deny();
        }

        return $this->requireAbility($user, Ability::DeleteAndRestoreBookingsOfEvent);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Booking $booking): Response
    {
        if (!$booking->trashed()) {
            return $this->deny();
        }

        return $this->requireAbility($user, Ability::DeleteAndRestoreBookingsOfEvent);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Booking $booking): Response
    {
        return $this->deny();
    }

    public function manageGroup(User $user, Booking $booking): Response
    {
        return $this->requireAbility($user, Ability::ManageGroupsOfEvent);
    }
}
