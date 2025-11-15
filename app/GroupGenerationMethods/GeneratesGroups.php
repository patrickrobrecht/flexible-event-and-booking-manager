<?php

namespace App\GroupGenerationMethods;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

interface GeneratesGroups
{
    /**
     * @param Collection<int, Booking> $bookings
     *
     * @return array<int, Collection<int, Booking>>
     */
    public function generateGroups(int $groupsCount, Collection $bookings): array;
}
