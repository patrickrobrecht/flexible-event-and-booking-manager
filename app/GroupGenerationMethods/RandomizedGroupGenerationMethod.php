<?php

namespace App\GroupGenerationMethods;

use App\Enums\GroupGenerationMethod;
use App\Models\Booking;
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
