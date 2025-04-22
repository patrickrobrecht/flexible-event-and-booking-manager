<?php

namespace App\GroupGenerationMethods;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

abstract class GeneralGroupGenerationMethod implements GeneratesGroups
{
    protected int $groupsCount;
    private int $groupsWithAdditionalBookings;
    private int $bookingsPerGroup;

    public function generateGroups(int $groupsCount, Collection $bookings): array
    {
        $this->groupsCount = $groupsCount;

        $bookingsCount = $bookings->count();
        $this->bookingsPerGroup = (int) floor($bookingsCount / $groupsCount);
        $this->groupsWithAdditionalBookings = $bookingsCount - ($this->bookingsPerGroup * $groupsCount);

        return $this->buildGroups(
            $this->sortBookings($bookings)
        );
    }

    /**
     * Generate $groupsWithoutAdditionalBookings             groups with each $bookingsPerGroup     bookings
     *      and $groupsCount - $groupsWithAdditionalBookings groups with each $bookingsPerGroup + 1 bookings.
     *
     * @param  Collection<int, Booking> $bookings
     *
     * @return array<Collection<int, Booking>>
     */
    protected function buildGroups(
        Collection $bookings
    ): array {
        $bookingsAssignedAlready = 0;
        $groups = [];
        for ($i = 1; $i <= $this->groupsCount; $i++) {
            $bookingsInGroupCount = $i <= $this->groupsWithAdditionalBookings
                ? $this->bookingsPerGroup + 1
                : $this->bookingsPerGroup;

            $groups[$i] = $bookings->slice($bookingsAssignedAlready, $bookingsInGroupCount);
            $bookingsAssignedAlready += $bookingsInGroupCount;
        }

        return $groups;
    }

    /**
     * @param Collection<int, Booking> $bookings
     *
     * @return Collection<int, Booking>
     */
    abstract protected function sortBookings(Collection $bookings): Collection;
}
