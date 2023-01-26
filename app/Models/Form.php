<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property-read  int $id
 * @property string $name
 *
 * @property Collection|BookingOption[] $bookingOptions {@see self::bookingOptions()}
 * @property Collection|FormFieldGroup[] $formFieldGroups {@see self::formFieldGroups()}
 */
class Form extends Model
{
    use HasFactory;

    protected $casts = [
        'maximum_submissions' => 'integer',
    ];

    protected $fillable = [
        'name',
    ];

    protected $perPage = 12;

    public function bookingOptions(): HasMany
    {
        return $this->hasMany(BookingOption::class);
    }

    public function formFieldGroups(): HasMany
    {
        return $this->hasMany(FormFieldGroup::class)
                    ->orderBy('sort');
    }

    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);

        return $this->save();
    }
}
