<?php

namespace App\GroupGenerationMethods;

use App\Enums\GroupGenerationMethod;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

/**
 * Implementation of {@see GroupGenerationMethod::Randomized}.
 */
class RandomizedGroupGenerationMethod extends GeneralGroupGenerationMethod
{
    /**
     * @param Collection<int, Booking> $bookings
     *
     * @return Collection<int, Booking>
     */
    protected function sortBookings(Collection $bookings): Collection
    {
        return $bookings->shuffle();
    }
}
