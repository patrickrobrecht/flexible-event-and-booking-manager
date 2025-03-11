<?php

namespace App\Models;

use App\Enums\FormElementType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $value
 *
 * @property-read string $file_extension {@see self::fileExtension()}
 * @property-read string $file_name_for_download {@see self::fileNameForDownload()}
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

    protected function fileExtension(): Attribute
    {
        return Attribute::get(fn () => Str::of($this->value)->afterLast('.'));
    }

    protected function fileNameForDownload(): Attribute
    {
        return Attribute::get(function () {
            if ($this->formField->type !== FormElementType::File) {
                return null;
            }

            return $this->booking->prefixFileNameWithGroup(
                $this->booking->file_name
                . '-' . Str::slug($this->formField->name)
                . '.' . $this->file_extension
            );
        });
    }

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
        } elseif (in_array($this->formField->type, [FormElementType::Date, FormElementType::DateTime], true)) {
            $this->casts['value'] = 'datetime';
        }
    }

    public function getRealValue(): mixed
    {
        $this->forceCast();

        return $this->value;
    }
}
