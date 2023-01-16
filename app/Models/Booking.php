<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Models\Traits\HasAddress;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property-read int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $email
 * @property ?string $phone
 * @property ?Carbon $booked_at
 * @property ?float $price
 *
 * @property-read ?User $bookedByUser {@see self::bookedByUser()}
 * @property-read BookingOption $bookingOption {@see self::bookingOption()}
 * @property-read Collection|FormFieldValue[] $formFieldValues {@see self::formFieldValues()}
 */
class Booking extends Model
{
    use Filterable;
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

    public function bookedByUser()
    {
        return $this->belongsTo(User::class, 'booked_by_user_id');
    }

    public function bookingOption(): BelongsTo
    {
        return $this->belongsTo(BookingOption::class, 'booking_option_id');
    }

    public function formFieldValues(): HasMany
    {
        return $this->hasMany(FormFieldValue::class)
                    ->with('formField');
    }

    public function scopeSearchAll(Builder $query, string ...$searchTerms): Builder
    {
        return $this->scopeIncludeColumns($query, ['first_name', 'last_name'], true, ...$searchTerms);
    }

    public function fillAndSave(array $validatedData): bool
    {
        if (!$this->fill($validatedData)->save()) {
            return false;
        }

        foreach ($this->bookingOption->form->formFieldGroups ?? [] as $group) {
            foreach ($group->formFields as $field) {
                if (!isset($field->column)) {
                    if (!$this->setFieldValue($field, $validatedData[$field->input_name] ?? null)) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function setFieldValue(FormField $formField, mixed $value): bool
    {
        $formFieldValue = $this->formFieldValues->loadMissing(['formField'])->first(
            static fn (FormFieldValue $existingFormFieldValue) => $existingFormFieldValue->formField->is($formField)
        );

        if ($formFieldValue === null) {
            /** @var FormFieldValue $formFieldValue */
            $formFieldValue = $this->formFieldValues()->make();
            $formFieldValue->formField()->associate($formField);
        }

        $formFieldValue->forceCast();
        $formFieldValue->value = $value;
        return $formFieldValue->save();
    }

    public function getFieldValue(FormField $formField): mixed
    {
        if (isset($formField->column)) {
            return $this->attributes[$formField->column] ?? null;
        }

        /** @var ?FormFieldValue $fieldValue */
        $fieldValue = $this->formFieldValues
            ->first(
                static fn (FormFieldValue $formFieldValue) => $formFieldValue->formField->column === null
                    && $formFieldValue->formField->is($formField)
            );
        if ($fieldValue) {
            return $fieldValue->getRealValue();
        }

        return null;
    }

    public static function allowedFilters(): array
    {
        return [
            /** @see self::scopeSearchAll() */
            AllowedFilter::scope('search', 'searchAll'),
        ];
    }
}
