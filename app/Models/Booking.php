<?php

namespace App\Models;

use App\Models\Traits\HasAddress;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property-read int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property ?string $phone
 * @property ?Carbon $booked_at
 *
 * @property-read BookingOption $bookingOption {@see self::bookingOption()}
 */
class Booking extends Model
{
    use HasAddress;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'street',
        'house_number',
        'postal_code',
        'city',
        'country',
        'booked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'booked_at' => 'datetime',
    ];

    public function bookingOption(): BelongsTo
    {
        return $this->belongsTo(BookingOption::class, 'booking_option_id');
    }
}
