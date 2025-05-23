<?php

namespace App\Models;

use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\QueryBuilder\SortOptions;
use App\Models\Traits\FiltersByRelationExistence;
use App\Models\Traits\HasAddress;
use App\Models\Traits\HasIdForRouting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property-read int $id
 * @property ?string $name
 * @property ?string $website_url
 *
 * @property-read string[] $fullAddressBlock {@see self::fullAddressBlock()}
 * @property-read string $nameOrAddress {@see self::nameOrAddress()}
 *
 * @property-read Collection|Event[] $events {@see self::events()}
 * @property-read Collection|Organization[] $organizations {@see self::organizations()}
 */
class Location extends Model
{
    use BuildsQueryFromRequest;
    use FiltersByRelationExistence;
    use HasAddress;
    use HasFactory;
    use HasIdForRouting;

    protected $fillable = [
        'name',
        'street',
        'house_number',
        'postal_code',
        'city',
        'country',
        'website_url',
    ];

    public function fullAddressBlock(): Attribute
    {
        return new Attribute(fn () => array_merge(
            isset($this->name) ? [$this->name] : [],
            $this->addressBlock
        ));
    }

    public function nameOrAddress(): Attribute
    {
        return new Attribute(fn () => $this->name
                                      ?? sprintf('%s, %s', $this->streetLine, $this->city));
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function organizations(): HasMany
    {
        return $this->hasMany(Organization::class);
    }

    public function scopeEvent(Builder $query, int|string $eventId): Builder
    {
        return $this->scopeRelation($query, $eventId, 'events', fn (Builder $q) => $q->where('id', '=', $eventId));
    }

    public function scopeOrganization(Builder $query, int|string $eventId): Builder
    {
        return $this->scopeRelation($query, $eventId, 'organizations', fn (Builder $q) => $q->where('organization_id', '=', $eventId));
    }

    /**
     * @param array<string, mixed> $validatedData
     */
    public function fillAndSave(array $validatedData): bool
    {
        return $this->fill($validatedData)->save();
    }

    /**
     * @return AllowedFilter[]
     */
    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            /** @see HasAddress::scopeAddressFields() */
            AllowedFilter::scope('address', 'addressFields'),
            /** @see self::scopeEvent() */
            AllowedFilter::scope('event_id', 'event'),
            /** @see self::scopeOrganization() */
            AllowedFilter::scope('organization_id', 'organization'),
        ];
    }

    public static function sortOptions(): SortOptions
    {
        return self::sortOptionsForNameAndTimeStamps();
    }
}
