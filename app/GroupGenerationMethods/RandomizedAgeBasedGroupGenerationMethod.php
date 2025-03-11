<?php

namespace App\GroupGenerationMethods;

use App\Enums\GroupGenerationMethod;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;

/**
 * Implementation of {@see GroupGenerationMethod::RandomizedAndAgeBased}
 */
class RandomizedAgeBasedGroupGenerationMethod extends AgeBasedGroupGenerationMethod
{
    /**
     * @param Collection<Booking> $bookings
     *
     * @return Collection<Booking>
     */
    protected function sortBookings(Collection $bookings): Collection
    {
        $bookings = parent::sortBookings($bookings);

        $sortedBookings = [];
        foreach ($bookings->chunk($this->groupsCount) as $chunkOfBookings) {
            $counter = 0;
            foreach ($chunkOfBookings->shuffle() as $shuffledBooking) {
                $sortedBookings[$counter][] = $shuffledBooking;
                $counter++;
            }
        }

        return Collection::make(Arr::collapse($sortedBookings));
    }
}
