<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property ?string $description
 *
 * @mixin Model
 */
trait HasNameAndDescription
{
    use Searchable;

    public function scopeSearchNameAndDescription(Builder $query, string ...$searchTerms): Builder
    {
        return $this->scopeIncludeColumns($query, ['name', 'description'], true, ...$searchTerms);
    }
}
