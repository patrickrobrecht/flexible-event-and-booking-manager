<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Options\Ability;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property-read int $id
 * @property string $name
 * @property string[] $abilities
 *
 * @property-read Collection|User[] $users {@see UserRole::users()}
 */
class UserRole extends Model
{
    use Filterable;
    use HasFactory;

    protected $casts = [
        'abilities' => 'json',
    ];

    protected $fillable = [
        'name',
        'abilities',
    ];

    protected $perPage = 12;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function fillAndSave(array $validatedData): bool
    {
        return $this->fill($validatedData)->save();
    }

    public function hasAbility(Ability $ability): bool
    {
        return in_array($ability->value, $this->abilities, true);
    }

    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
        ];
    }
}
