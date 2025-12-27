<?php

namespace App\Models;

use App\Enums\FormElementType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property int $sort
 * @property string $name
 * @property ?string $hint
 * @property ?string $container_class
 * @property ?string $column
 * @property FormElementType $type
 * @property bool $required
 * @property string[]|null $validation_rules
 * @property string[]|null $allowed_values
 * @property bool $editable_after_submission
 * @property string $input_name {@see self::inputName()}
 * @property BookingOption $bookingOption {@see self::bookingOption()}
 * @property Collection|FormFieldValue[] $formFieldValue {@see self::formFieldValues()}
 */
class FormField extends Model
{
    use HasFactory;

    protected $casts = [
        'sort' => 'integer',
        'type' => FormElementType::class,
        'required' => 'boolean',
        'validation_rules' => 'json',
        'allowed_values' => 'json',
        'editable_after_submission' => 'boolean',
    ];

    protected $fillable = [
        'sort',
        'name',
        'hint',
        'container_class',
        'column',
        'type',
        'required',
        'validation_rules',
        'allowed_values',
        'editable_after_submission',
    ];

    public function inputName(): Attribute
    {
        return new Attribute(fn () => $this->column ?? 'custom-' . $this->id);
    }

    public function bookingOption(): BelongsTo
    {
        return $this->belongsTo(BookingOption::class);
    }

    /**
     * @return HasMany<FormFieldValue, $this>
     */
    public function formFieldValues(): HasMany
    {
        return $this->hasMany(FormFieldValue::class);
    }

    /**
     * @param array<string, mixed> $validatedData
     */
    public function fillAndSave(array $validatedData): bool
    {
        return $this->fill($validatedData)->save();
    }

    public function isDate(): bool
    {
        return $this->type === FormElementType::Date;
    }

    public function isSingleCheckbox(): bool
    {
        return $this->type === FormElementType::Checkbox
               && count($this->allowed_values ?? []) <= 1;
    }

    public function isMultiCheckbox(): bool
    {
        return $this->type === FormElementType::Checkbox
               && count($this->allowed_values ?? []) > 1;
    }
}
