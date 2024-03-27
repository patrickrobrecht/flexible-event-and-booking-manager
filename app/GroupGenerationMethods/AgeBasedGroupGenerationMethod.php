<?php

namespace App\GroupGenerationMethods;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

/**
 * Implementation of {@see GroupGenerationMethod::AgeBased}
 */
class AgeBasedGroupGenerationMethod extends GeneralGroupGenerationMethod
{
    /**
     * @param Collection<Booking> $bookings
     *
     * @return Collection<Booking>
     */
    protected function sortBookings(Collection $bookings): Collection
    {
        return $bookings->sortBy('date_of_birth');
    }
}
