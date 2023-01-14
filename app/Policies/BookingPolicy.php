<?php

namespace App\Policies;

use App\Models\Booking;
use App\Models\User;
use App\Options\Ability;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class BookingPolicy
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
        return $this->requireAbility($user, Ability::ViewBookingsOfEvent);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param Booking $booking
     *
     * @return Response
     */
    public function view(User $user, Booking $booking): Response
    {
        return $this->viewAny($user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param User $user
     *
     * @return Response
     */
    public function create(User $user): Response
    {
        return $this->allow();
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param Booking $booking
     *
     * @return Response
     */
    public function update(User $user, Booking $booking): Response
    {
        return $this->requireAbility($user, Ability::EditBookingsOfEvent);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param Booking $booking
     *
     * @return Response
     */
    public function delete(User $user, Booking $booking): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param Booking $booking
     *
     * @return Response
     */
    public function restore(User $user, Booking $booking): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param Booking $booking
     *
     * @return Response
     */
    public function forceDelete(User $user, Booking $booking): Response
    {
        return $this->deny();
    }
}
