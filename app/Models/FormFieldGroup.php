<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read int $id
 * @property int $sort
 * @property string $name
 * @property bool $show_name
 * @property ?string $description
 *
 * @property-read Form $form {@see self::form()}
 * @property-read Collection|FormField[] $formFields {@see self::formFields()}
 */
class FormFieldGroup extends Model
{
    protected $casts = [
        'show_name' => 'boolean',
    ];

    protected $fillable = [
        'sort',
        'name',
        'show_name',
        'description',
    ];

    public function form(): BelongsTo
    {
        return $this->belongsTo(Form::class, 'form_id');
    }

    public function formFields(): HasMany
    {
        return $this->hasMany(FormField::class, 'form_field_group_id')
                    ->orderBy('sort');
    }

    public function addField(array $validatedData): FormField
    {
        return $this->formFields()->create($validatedData);
    }
}
