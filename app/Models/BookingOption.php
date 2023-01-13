<?php

namespace App\Models;

use App\Models\Traits\HasSlugForRouting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property ?int $maximum_bookings
 * @property ?Carbon $available_from
 * @property ?Carbon $available_until
 * @property float $price
 * @property array $price_conditions
 * @property array $restrictions
 *
 * @property-read Collection|Booking[] $bookings {@see self::bookings()}
 * @property-read Event $event {@see self::event()}
 */
class BookingOption extends Model
{
    use HasSlugForRouting;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'maximum_bookings',
        'available_from',
        'available_until',
        'price',
        'book_for_self_only',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'maximum_bookings' => 'integer',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'price' => 'float',
        'book_for_self_only' => 'bool',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'booking_option_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function fillAndSave(array $validatedData): bool
    {
        return $this->fill($validatedData)->save();
    }
}
