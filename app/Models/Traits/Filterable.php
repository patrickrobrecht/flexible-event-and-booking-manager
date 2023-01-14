<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @mixin Model
 */
trait Filterable
{
    public function scopeOrderedByDefault(Builder $query): Builder
    {
        $columns = self::defaultSorts();

        foreach ($columns as $column) {
            $query->orderBy($column);
        }

        return $query;
    }

    public static function filter(Builder|Relation|string|null $subject = null): QueryBuilder
    {
        $query = QueryBuilder::for($subject ?? self::class)
            ->allowedFilters(self::allowedFilters());

        $sorts = self::defaultSorts();

        if (count($sorts) > 0) {
            $query->defaultSorts(...self::defaultSorts());
        }

        return $query;
    }

    /**
     * @return AllowedFilter[]
     */
    abstract public static function allowedFilters(): array;

    public static function defaultValuesForFilters(): array
    {
        $filterSuffix = config('query-builder.parameters.filter') . '.';
        $defaults = [];
        foreach (self::allowedFilters() as $allowedFilter) {
            if ($allowedFilter->hasDefault()) {
                $defaults[$filterSuffix . $allowedFilter->getName()] = $allowedFilter->getDefault();
            }
        }
        return $defaults;
    }

    public static function defaultSorts(): array
    {
        return [];
    }
}
