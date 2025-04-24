<?php

namespace App\Models;

use App\Enums\MaterialStatus;
use App\Models\QueryBuilder\BuildsQueryFromRequest;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property-read int $id
 * @property string $name
 * @property ?string $description
 * @property ?string $packaging_instructions
 * @property ?int $parent_storage_location_id
 *
 * @property int $child_storage_locations_count
 * @property-read \Illuminate\Support\Collection<int, MaterialStatus> $material_statuses {@see self::materialStatuses()}
 * @property int $materials_count
 *
 * @property-read Collection|StorageLocation[] $childStorageLocations {@see self::childStorageLocations()}
 * @property-read Collection|Material[] $materials {@see self::materials()}
 * @property-read ?StorageLocation $parentStorageLocation {@see self::parentStorageLocation()}
 * @property-read MaterialStorageLocation $pivot {@see self::materials()}
 */
class StorageLocation extends Model
{
    use BuildsQueryFromRequest;
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'packaging_instructions',
    ];

    public function materialStatuses(): Attribute
    {
        return Attribute::get(
            fn () => $this->materials
                ->map(fn ($material) => $material->pivot->material_status)
                ->unique()
                ->sortBy('value')
        );
    }

    public function childStorageLocations(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_storage_location_id');
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class)
            ->withPivot(MaterialStorageLocation::PIVOT_COLUMNS)
            ->using(MaterialStorageLocation::class)
            ->withTimestamps();
    }

    public function parentStorageLocation(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_storage_location_id');
    }

    /**
     * @param array<string, mixed> $validatedData
     */
    public function fillAndSave(array $validatedData): bool
    {
        return $this->fill($validatedData)->save();
    }

    public function getChildStorageLocationsCount(): int
    {
        if (!isset($this->child_storage_locations_count)) {
            if ($this->relationLoaded('childStorageLocations')) {
                $this->child_storage_locations_count = $this->childStorageLocations->count();
            } else {
                $this->loadCount('childStorageLocations');
            }
        }

        return $this->child_storage_locations_count;
    }

    public function getMaterialsCount(): int
    {
        if (!isset($this->materials_count)) {
            if ($this->relationLoaded('materials')) {
                $this->materials_count = $this->materials->count();
            } else {
                $this->loadCount('materials');
            }
        }

        return $this->materials_count;
    }

    /**
     * @return array<int, AllowedFilter>
     */
    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::partial('description'),
        ];
    }
}
