<?php

namespace App\GroupGenerationMethods;

use App\Models\Booking;
use App\Options\GroupGenerationMethod;
use Illuminate\Database\Eloquent\Collection;

/**
 * Implementation of {@see GroupGenerationMethod::Randomized}
 */
class RandomizedGroupGenerationMethod extends GeneralGroupGenerationMethod
{
    /**
     * @param Collection<Booking> $bookings
     *
     * @return Collection<Booking>
     */
    protected function sortBookings(Collection $bookings): Collection
    {
        return $bookings->shuffle();
    }
}
