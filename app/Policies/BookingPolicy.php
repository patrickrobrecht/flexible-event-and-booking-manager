<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\User;
use App\Options\Ability;
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
    public function create(User $user): Response
    {
        /** @see BookingOptionPolicy::book() */
        return $this->deny();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Booking $booking): Response
    {
        return $this->requireAbility($user, Ability::EditBookingsOfEvent);
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
