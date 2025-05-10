<?php

namespace App\Models\QueryBuilder;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @mixin Model
 */
trait Filterable
{
    /**
     * @return AllowedFilter[]
     */
    public static function allowedFilters(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultValuesForFilters(): array
    {
        /** @phpstan-ignore-next-line binaryOp.invalid */
        $filterSuffix = config('query-builder.parameters.filter') . '.';

        $defaults = [];
        foreach (self::allowedFilters() as $allowedFilter) {
            if ($allowedFilter->hasDefault()) {
                $defaults[$filterSuffix . $allowedFilter->getName()] = $allowedFilter->getDefault();
            }
        }

        return $defaults;
    }
}
