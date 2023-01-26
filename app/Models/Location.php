<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Models\Traits\HasAddress;
use App\Models\Traits\HasWebsite;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property-read int $id
 * @property string $name
 *
 * @property-read string[] $fullAddressBlock {@see Location::fullAddressBlock()}
 * @property-read string $nameOrAddress {@see Location::nameOrAddress()}
 *
 * @property-read Collection|Event $events {@see Location::events()}
 * @property-read Collection|Organization $organizations {@see Location::organizations()}
 */
class Location extends Model
{
    use Filterable;
    use HasAddress;
    use HasFactory;
    use HasWebsite;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'street',
        'house_number',
        'postal_code',
        'city',
        'country',
        'website_url',
    ];

    protected $perPage = 12;

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

    public function fillAndSave(array $validatedData): bool
    {
        return $this->fill($validatedData)->save();
    }

    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            /** @see HasAddress::scopeAddressFields() */
            AllowedFilter::scope('address', 'addressFields')
        ];
    }
}
