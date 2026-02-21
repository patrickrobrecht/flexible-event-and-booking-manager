<?php

namespace App\Models\Traits;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingOption;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property-read Collection<int, Booking> $bookings {@see self::bookings()}
 * @property-read Collection<int, Booking> $bookingsConfirmed {@see self::bookingsConfirmed()}
 * @property-read Collection<int, Booking> $bookingsOnWaitingList {@see self::bookingsOnWaitingList()}
 */
trait HasBookings
{
    /**
     * @return HasMany<Booking, $this>|HasManyThrough<Booking, BookingOption, $this>
     */
    abstract public function bookings(): HasMany|HasManyThrough;

    /**
     * @return HasMany<Booking, $this>|HasManyThrough<Booking, BookingOption, $this>
     */
    public function bookingsConfirmed(): HasMany|HasManyThrough
    {
        return $this->bookings()
            ->where('status', '=', BookingStatus::Confirmed);
    }

    /**
     * @return HasMany<Booking, $this>|HasManyThrough<Booking, BookingOption, $this>
     */
    public function bookingsOnWaitingList(): HasMany|HasManyThrough
    {
        return $this->bookings()
            ->where('status', '=', BookingStatus::Waiting);
    }
}
