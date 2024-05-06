<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FiltersByRelationExistence
{
    public function scopeRelation(Builder $query, int|string $value, string $relation, \Closure $callback): Builder
    {
        return match ($value) {
            '', '*' => $query,
            '-' => $query->whereDoesntHave($relation),
            '+' => $query->whereHas($relation),
            default => $query->whereHas($relation, $callback),
        };
    }
}
