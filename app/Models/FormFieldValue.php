<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $value
 *
 * @property Booking $booking {@see FormFieldValue::booking()}
 * @property FormField $formField {@see FormFieldValue::formField()}
 */
class FormFieldValue extends Model
{
    use HasFactory;

    protected $fillable = [
        'value',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function formField(): BelongsTo
    {
        return $this->belongsTo(FormField::class);
    }

    public function forceCast(): void
    {
        if ($this->formField->isMultiCheckbox()) {
            $this->casts['value'] = 'json';
        } elseif ($this->formField->isSingleCheckbox()) {
            $this->casts['value'] = 'integer';
        }
    }

    public function getRealValue(): mixed
    {
        $this->forceCast();

        return $this->value;
    }
}
