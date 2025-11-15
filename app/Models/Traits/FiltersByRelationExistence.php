<?php

namespace App\Models\Traits;

use App\Enums\FilterValue;
use Closure;
use Illuminate\Database\Eloquent\Builder;

trait FiltersByRelationExistence
{
    public function scopeRelation(Builder $query, int|string $value, string $relation, Closure $callback): Builder
    {
        return match ($value) {
            FilterValue::All->value => $query,
            FilterValue::With->value => $query->whereHas($relation),
            FilterValue::Without->value => $query->whereDoesntHave($relation),
            default => $query->whereHas($relation, $callback),
        };
    }
}
