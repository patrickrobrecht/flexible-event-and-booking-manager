<?php

namespace App\Models;

use App\Models\Traits\HasNameAndDescription;
use App\Models\Traits\HasSlugForRouting;
use App\Options\BookingRestriction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property ?string $description
 * @property ?int $maximum_bookings
 * @property ?Carbon $available_from
 * @property ?Carbon $available_until
 * @property ?float $price
 * @property array $price_conditions
 * @property array $restrictions
 *
 * @property-read Collection|Booking[] $bookings {@see self::bookings()}
 * @property-read Event $event {@see self::event()}
 * @property-read Collection|FormField[] $formFields {@see self::formFields()}
 */
class BookingOption extends Model
{
    use HasFactory;
    use HasNameAndDescription;
    use HasSlugForRouting;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'maximum_bookings',
        'available_from',
        'available_until',
        'price',
        'price_conditions',
        'restrictions',
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
        'price_conditions' => 'json',
        'restrictions' => 'json', /* @see BookingRestriction */
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'booking_option_id');
    }

    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(FormField::class, 'booking_option_id')
                    ->orderBy('sort');
    }

    public function fillAndSave(array $validatedData): bool
    {
        return $this->fill($validatedData)->save();
    }

    public function getFilePath(): string
    {
        return implode('/', [
            'bookings',
            $this->event_id,
            $this->id,
        ]);
    }

    public function isRestrictedBy(BookingRestriction $restriction): bool
    {
        return in_array($restriction->value, $this->restrictions ?? [], true);
    }

    public function hasReachedMaximumBookings(): bool
    {
        return isset($this->maximum_bookings)
               && $this->bookings->count() >= $this->maximum_bookings;
    }
}
