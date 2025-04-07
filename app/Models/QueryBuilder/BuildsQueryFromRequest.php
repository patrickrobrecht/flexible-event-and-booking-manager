<?php

namespace App\Models\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @mixin Model
 */
trait BuildsQueryFromRequest
{
    use Filterable;
    use Sortable;

    /**
     * @template TRelatedModel of \Illuminate\Database\Eloquent\Model
     * @template TDeclaringModel of \Illuminate\Database\Eloquent\Model
     *
     * @param Builder<self>|Relation<TRelatedModel, TDeclaringModel, self>|string|null $subject
     */
    public static function buildQueryFromRequest(Builder|Relation|string|null $subject = null): QueryBuilder
    {
        $defaultSorts = self::defaultSorts();
        if (count($defaultSorts) === 0) {
            $defaultSorts = self::firstAllowedSort();
        }

        return QueryBuilder::for($subject ?? self::class)
            ->allowedFilters(self::allowedFilters())
            ->allowedSorts(self::allowedSorts())
            ->defaultSorts($defaultSorts);
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaultValuesForQuery(): array
    {
        $defaultSort = self::firstDefaultSort() ?? self::firstAllowedSort();

        return [
            ...self::defaultValuesForFilters(),
            config('query-builder.parameters.sort') => $defaultSort instanceof AllowedSort ? $defaultSort->getName() : null,
        ];
    }
}
