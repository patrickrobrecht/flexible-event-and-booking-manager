<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property string $street
 * @property string $house_number
 * @property string $postal_code
 * @property string $city
 * @property string $country
 *
 * @property-read string[] $addressBlock {@see HasAddress::addressBlock()}
 * @property-read string $addressLine {@see HasAddress::addressLine()}
 * @property-read string $streetLine {@see HasAddress::streetLine()}
 * @property-read string $cityLine {@see HasAddress::cityLine()}
 */
trait HasAddress
{
    use Searchable;

    public static array $addressFields = [
        'street',
        'house_number',
        'postal_code',
        'city',
        'country',
    ];

    public function addressBlock(): Attribute
    {
        return new Attribute(fn () => [
            $this->streetLine,
            $this->cityLine,
            $this->country,
        ]);
    }

    public function addressLine(): Attribute
    {
        return new Attribute(fn () => implode(', ', $this->addressBlock));
    }

    public function cityLine(): Attribute
    {
        return new Attribute(fn () => sprintf('%s %s', $this->postal_code, $this->city));
    }

    public function streetLine(): Attribute
    {
        return new Attribute(fn () => sprintf('%s %s', $this->street, $this->house_number));
    }

    public function scopeAddressFields(Builder $query, ...$searchTerms): Builder
    {
        return $this->scopeIncludeColumns($query, self::$addressFields, true, ...$searchTerms);
    }

    public function hasAnyFilledAddressField(): bool
    {
        return $this->street !== null
            || $this->house_number !== null
            || $this->postal_code !== null
            || $this->city !== null
            || $this->country !== null;
    }
}
