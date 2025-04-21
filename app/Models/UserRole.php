<?php

namespace App\Models;

use App\Enums\Ability;
use App\Enums\FilterValue;
use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\QueryBuilder\SortOptions;
use App\Models\Traits\FiltersByRelationExistence;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\SortDirection;

/**
 * @property-read int $id
 * @property string $name
 * @property string[] $abilities
 *
 * @property-read Collection|User[] $users {@see UserRole::users()}
 */
class UserRole extends Model
{
    use BuildsQueryFromRequest;
    use FiltersByRelationExistence;
    use HasFactory;

    protected $casts = [
        'abilities' => 'json',
    ];

    protected $fillable = [
        'name',
        'abilities',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->withTimestamps();
    }

    public function scopeUser(Builder $query, int|string $userId): Builder
    {
        return $this->scopeRelation($query, $userId, 'users', fn (Builder $q) => $q->where('user_id', '=', $userId));
    }

    public function deleteAfterDetachingUsers(): bool
    {
        $this->users()->detach();
        return $this->delete();
    }

    /**
     * @param array<string, mixed> $validatedData
     */
    public function fillAndSave(array $validatedData): bool
    {
        return $this->fill($validatedData)->save();
    }

    public function hasAbility(Ability $ability): bool
    {
        return in_array($ability->value, $this->abilities, true);
    }

    /**
     * @return array<int, AllowedFilter>
     */
    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            /** @see self::scopeUser() */
            AllowedFilter::scope('user_id', 'user')
                ->default(FilterValue::All->value),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function filterOptions(): array
    {
        return [
            FilterValue::All->value => __('all'),
            FilterValue::With->value => __('with at least one user role'),
            FilterValue::Without->value => __('without user role'),
        ];
    }

    public static function sortOptions(): SortOptions
    {
        return self::sortOptionsForNameAndTimeStamps()
            ->addBothDirections(
                __('Number of users'),
                AllowedSort::callback(
                    'users_count',
                    fn (Builder $query, bool $descending, string $property) => $query
                        ->withCount('users')
                        ->orderBy('users_count', $descending ? SortDirection::DESCENDING : SortDirection::ASCENDING)
                )
            );
    }
}
