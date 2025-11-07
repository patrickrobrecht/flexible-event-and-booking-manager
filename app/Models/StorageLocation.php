<?php

namespace App\Models;

use App\Enums\FilterValue;
use App\Enums\MaterialStatus;
use App\Models\QueryBuilder\BuildsQueryFromRequest;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property-read int $id
 * @property string $name
 * @property ?string $description
 * @property ?string $packaging_instructions
 * @property ?int $parent_storage_location_id
 * @property int $child_storage_locations_count
 * @property-read \Illuminate\Support\Collection<int, MaterialStatus> $material_statuses {@see self::materialStatuses()}
 * @property int $materials_count
 * @property-read Collection|StorageLocation[] $childStorageLocations {@see self::childStorageLocations()}
 * @property-read Collection|Material[] $materials {@see self::materials()}
 * @property-read ?StorageLocation $parentStorageLocation {@see self::parentStorageLocation()}
 * @property-read MaterialStorageLocation $pivot {@see self::materials()}
 */
class StorageLocation extends Model
{
    use BuildsQueryFromRequest;
    use HasFactory;

    public const int MAX_CHILD_LEVELS = 5;

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
        return $this->hasMany(__CLASS__, 'parent_storage_location_id')
            ->orderBy('name');
    }

    public function materials(): BelongsToMany
    {
        return $this->belongsToMany(Material::class)
            ->withPivot(MaterialStorageLocation::PIVOT_COLUMNS)
            ->using(MaterialStorageLocation::class)
            ->withTimestamps()
            ->orderBy('name');
    }

    public function parentStorageLocation(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_storage_location_id');
    }

    /**
     * @param array{parent_storage_location_id: ?int} $validatedData
     */
    public function fillAndSave(array $validatedData): bool
    {
        $this->parentStorageLocation()->associate($validatedData['parent_storage_location_id']);
        return $this->fill($validatedData)->save();
    }

    /**
     * @return array<int, self>
     */
    public function getAncestors(): array
    {
        $storageLocation = $this->parentStorageLocation ?? null;
        $path = [];
        while ($storageLocation) {
            $path[] = $storageLocation;
            $storageLocation = $storageLocation->parentStorageLocation ?? null;
        }

        return array_reverse($path);
    }

    /**
     * @return non-empty-array<int, self>
     */
    public function getAncestorsAndSelf(): array
    {
        return [
            ...$this->getAncestors(),
            $this,
        ];
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

    /**
     * @return array<int, self>
     */
    public function getDescendants(): array
    {
        $descendants = [
            $this->childStorageLocations,
        ];
        foreach ($this->childStorageLocations as $childStorageLocation) {
            $descendants[] = $childStorageLocation->getDescendants();
        }

        return Arr::flatten($descendants);
    }

    /**
     * @return non-empty-array<int, self>
     */
    public function getDescendantsAndSelf(): array
    {
        return [
            ...$this->getDescendants(),
            $this,
        ];
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

    public function getRoute(): string
    {
        return route('storage-locations.show', $this);
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

    /**
     * @return array<int, string>
     */
    public static function defaultSorts(): array
    {
        return [
            'name',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function filterOptions(): array
    {
        return [
            FilterValue::All->value => __('all'),
            FilterValue::With->value => __('with at least one storage location'),
            FilterValue::Without->value => __('without storage locations'),
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function relationsForChildStorageLocationsAndMaterial(): array
    {
        return \Illuminate\Support\Collection::make(range(0, self::MAX_CHILD_LEVELS))
            ->map(fn ($i) => str_repeat('childStorageLocations.', self::MAX_CHILD_LEVELS - $i) . 'materials')
            ->all();
    }
}
