<?php

namespace App\Models;

use App\Enums\BookingRestriction;
use App\Enums\FormElementType;
use App\Models\Traits\HasNameAndDescription;
use App\Models\Traits\HasSlugForRouting;
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
 * @property mixed[] $price_conditions
 * @property ?int $payment_due_days
 * @property string[] $restrictions
 * @property string $confirmation_text
 * @property-read Booking[]|Collection $bookings {@see self::bookings()}
 * @property-read Event $event {@see self::event()}
 * @property-read Collection|FormField[] $formFields {@see self::formFields()}
 * @property-read Collection|FormField[] $formFieldsForFiles {@see self::formFieldsForFiles()}
 */
class BookingOption extends Model
{
    use HasFactory;
    use HasNameAndDescription;
    use HasSlugForRouting;

    protected $casts = [
        'maximum_bookings' => 'integer',
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'price' => 'float',
        'price_conditions' => 'json',
        'payment_due_days' => 'integer',
        'restrictions' => 'json', /* @see BookingRestriction */
    ];

    protected $fillable = [
        'name',
        'slug',
        'description',
        'maximum_bookings',
        'available_from',
        'available_until',
        'price',
        'price_conditions',
        'payment_due_days',
        'restrictions',
        'confirmation_text',
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

    public function formFieldsForFiles(): HasMany
    {
        return $this->formFields()
            ->where('type', '=', FormElementType::File);
    }

    /**
     * @param array<string, mixed> $validatedData
     */
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

    public function getPaymentDeadline(?Carbon $bookedAt = null): Carbon
    {
        return ($bookedAt ?? Carbon::now())
            ->endOfDay()
            ->addWeekdays($this->payment_due_days ?? 0);
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
