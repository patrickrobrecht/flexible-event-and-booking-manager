<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

/**
 * @property string $first_name
 * @property string $last_name
 * @property-read string $greeting {@see self::greeting()}
 * @property-read string $name {@see self::name()}
 */
trait HasFullName
{
    public function greeting(): Attribute
    {
        return new Attribute(fn () => __('Hello :name,', ['name' => $this->name]));
    }

    public function name(): Attribute
    {
        return new Attribute(fn () => $this->first_name . ' ' . $this->last_name);
    }
}
