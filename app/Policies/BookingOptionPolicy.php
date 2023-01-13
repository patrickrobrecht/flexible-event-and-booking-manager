<?php

namespace App\Policies;

use App\Models\BookingOption;
use App\Models\User;
use App\Options\Ability;
use App\Policies\Traits\ChecksAbilities;
use Illuminate\Auth\Access\Response;

class BookingOptionPolicy
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
        return $this->deny();
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param User $user
     * @param BookingOption $bookingOption
     *
     * @return Response
     */
    public function view(User $user, BookingOption $bookingOption): Response
    {
        return $this->response($user->can('view', $bookingOption->event));
    }

    public function book(User $user, BookingOption $bookingOption): Response
    {
        return $this->response(
            // booking period has started
            isset($bookingOption->available_from) && $bookingOption->available_from->isPast()
            // ... and never ends or has not ended yet
            && (!isset($bookingOption->available_until) || $bookingOption->available_until->isFuture())
        );
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
        return $this->requireAbility($user, Ability::ManageBookingOptionsOfEvent);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param User $user
     * @param BookingOption $bookingOption
     *
     * @return Response
     */
    public function update(User $user, BookingOption $bookingOption): Response
    {
        return $this->create($user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param User $user
     * @param BookingOption $bookingOption
     *
     * @return Response
     */
    public function delete(User $user, BookingOption $bookingOption): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param User $user
     * @param BookingOption $bookingOption
     *
     * @return Response
     */
    public function restore(User $user, BookingOption $bookingOption): Response
    {
        return $this->deny();
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param User $user
     * @param BookingOption $bookingOption
     *
     * @return Response
     */
    public function forceDelete(User $user, BookingOption $bookingOption): Response
    {
        return $this->deny();
    }
}
