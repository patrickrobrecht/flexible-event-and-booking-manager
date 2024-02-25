<?php

namespace App\Models\QueryBuilder;

use Illuminate\Database\Eloquent\Model;
use Spatie\QueryBuilder\AllowedSort;

/**
 * @mixin Model
 */
trait Sortable
{
    /**
     * @return AllowedSort[]
     */
    public static function allowedSorts(): array
    {
        return self::sortOptions()->getAllowedSorts();
    }

    /**
     * @return AllowedSort[]
     */
    public static function defaultSorts(): array
    {
        return self::sortOptions()->getDefaultSorts();
    }

    public static function firstAllowedSort(): ?AllowedSort
    {
        $allowedSorts = self::allowedSorts();
        $allowedSort = reset($allowedSorts);
        if ($allowedSort instanceof AllowedSort) {
            return $allowedSort;
        }

        return null;
    }

    public static function firstDefaultSort(): ?AllowedSort
    {
        $defaultSorts = self::defaultSorts();
        $defaultSort = reset($defaultSorts);
        if ($defaultSort instanceof AllowedSort) {
            return $defaultSort;
        }

        return null;
    }

    public static function sortOptions(): SortOptions
    {
        return self::sortOptionsForTimeStamps();
    }

    public static function sortOptionsForNameAndTimeStamps(): SortOptions
    {
        return (new SortOptions())
            ->addBothDirections(__('Name'), AllowedSort::field('name'))
            ->merge(self::sortOptionsForTimeStamps());
    }

    public static function sortOptionsForTimeStamps(): SortOptions
    {
        return (new SortOptions())
            ->addBothDirections(__('Time of creation'), AllowedSort::field('created_at'))
            ->addBothDirections(__('Time of last update'), AllowedSort::field('updated_at'));
    }
}
