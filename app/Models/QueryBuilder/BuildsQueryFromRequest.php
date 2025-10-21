<?php

namespace App\Models\QueryBuilder;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
     * @template TDeclaringModel of Model
     *
     * @param Builder<self>|HasMany<self, TDeclaringModel>|string|null $subject
     * @return QueryBuilder<self>
     */
    public static function buildQueryFromRequest(Builder|Relation|string|null $subject = null): QueryBuilder
    {
        $defaultSorts = self::defaultSorts();
        if (count($defaultSorts) === 0) {
            $defaultSorts = self::firstAllowedSort();
        }
        /** @phpstan-var AllowedSort|non-empty-array<AllowedSort> $defaultSorts */

        /** @phpstan-ignore-next-line argument.type */
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
            /** @phpstan-ignore-next-line array.invalidKey */
            config('query-builder.parameters.sort') => $defaultSort instanceof AllowedSort ? $defaultSort->getName() : null,
        ];
    }
}
