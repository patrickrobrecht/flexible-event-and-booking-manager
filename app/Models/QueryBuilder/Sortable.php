<?php

namespace App\Models\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedSort;

/**
 * @mixin Model
 */
trait Sortable
{
    public function scopeOrderedByDefault(Builder $query): Builder
    {
        $columns = self::defaultSorts();

        foreach ($columns as $column) {
            $query->orderBy($column);
        }

        return $query;
    }

    public static function allowedSorts(): AllowedSorts
    {
        return self::allowedSortsByTimeStamps();
    }

    public static function allowedSortsByTimeStamps(): AllowedSorts
    {
        return (new AllowedSorts())
            ->addBothDirections(__('Time of creation'), AllowedSort::field('created_at'))
            ->addBothDirections(__('Time of last update'), AllowedSort::field('updated_at'));
    }

    public static function defaultSorts(): array
    {
        return [];
    }
}
