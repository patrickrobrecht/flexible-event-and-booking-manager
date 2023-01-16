<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property string $name
 * @property ?string $hint
 * @property ?string $container_class
 * @property ?string $column
 * @property string $type
 * @property bool $required
 * @property ?array $validation_rules
 * @property ?array $allowed_values
 *
 * @property string $input_name {@see self::inputName()}
 *
 * @property Form $form {@see self::form()}
 * @property Collection|FormFieldValue $formFieldValue {@see self::formFieldValues()}
 */
class FormField extends Model
{
    use HasFactory;

    protected $casts = [
        'required' => 'boolean',
        'validation_rules' => 'json',
        'allowed_values' => 'json',
    ];

    protected $fillable = [
        'name',
        'hint',
        'container_class',
        'column',
        'type',
        'required',
        'validation_rules',
        'allowed_rules',
    ];

    public function inputName(): Attribute
    {
        return new Attribute(fn () => $this->column ?? 'custom-' . $this->id);
    }

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class);
    }

    public function formFieldValues(): HasMany
    {
        return $this->hasMany(FormFieldValue::class);
    }

    public function fillAndSave(array $validatedData): bool
    {
        return $this->fill($validatedData)->save();
    }

    public function isSingleCheckbox(): bool
    {
        return $this->type === 'checkbox'
               && count($this->allowed_values ?? []) <= 1;
    }

    public function isMultiCheckbox(): bool
    {
        return $this->type === 'checkbox'
               && count($this->allowed_values ?? []) > 1;
    }
}
