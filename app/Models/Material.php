<?php

namespace App\Models;

use App\Enums\FilterValue;
use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\Traits\BelongsToOrganization;
use App\Models\Traits\FiltersByRelationExistence;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property-read int $id
 * @property string $name
 * @property ?string $description
 * @property-read int $organization_id
 *
 * @property-read Collection|StorageLocation[] $storageLocations {@see self::storageLocations()}
 * @property-read MaterialStorageLocation $pivot {@see self::storageLocations()}
 */
class Material extends Model
{
    use BelongsToOrganization;
    use BuildsQueryFromRequest;
    use FiltersByRelationExistence;
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function storageLocations(): BelongsToMany
    {
        return $this->belongsToMany(StorageLocation::class)
            ->withPivot(MaterialStorageLocation::PIVOT_COLUMNS)
            ->using(MaterialStorageLocation::class)
            ->withTimestamps();
    }

    public function scopeStorageLocation(Builder $query, int|string $storageLocationId): Builder
    {
        return $this->scopeRelation($query, $storageLocationId, 'storageLocations', fn (Builder $q) => $q->where('storage_location_id', '=', $storageLocationId));
    }

    public function deleteAfterDetachingStorageLocations(): ?bool
    {
        $this->storageLocations()->detach();
        return $this->delete();
    }

    /**
     * @param array{organization_id: int} $validatedData
     */
    public function fillAndSave(array $validatedData): bool
    {
        $this->organization()->associate($validatedData['organization_id']);
        return $this->fill($validatedData)->save();
    }

    public function getRoute(): string
    {
        return route('materials.show', $this);
    }

    /**
     * @return array<int, AllowedFilter>
     */
    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::partial('description'),
            AllowedFilter::exact('organization_id')
                ->ignore(FilterValue::All->value),
            /** @see self::scopeStorageLocation() */
            AllowedFilter::scope('storage_location_id', 'storageLocation')
                ->default(FilterValue::All->value),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function defaultSorts(): array
    {
        return [
            'name',
        ];
    }
}
