<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;

trait Searchable
{
    protected function scopeExclude(Builder $query, string $column, bool $like, int|string ...$searchTerms): Builder
    {
        if (count($searchTerms) === 0) {
            return $query;
        }

        $column = $query->qualifyColumn($column);

        if ($like) {
            foreach ($searchTerms as $searchTerm) {
                $query->where($column, 'NOT LIKE', self::searchTermForLike($searchTerm));
            }

            return $query;
        }

        if (count($searchTerms) === 1) {
            return $query->where($column, '!=', $searchTerms);
        }

        return $query->whereNotIn($column, $searchTerms);
    }

    protected function scopeIncludeColumns(
        Builder $query,
        array $columns,
        bool $like,
        int|string ...$searchTerms
    ): Builder {
        return $query->where(function (Builder $subQuery) use ($columns, $like, $searchTerms) {
            foreach ($columns as $column) {
                $subQuery->orWhere(fn($subQuery) => $this->scopeInclude($subQuery, $column, $like, ...$searchTerms));
            }
        });
    }

    protected function scopeInclude(Builder $query, string $column, bool $like, int|string ...$searchTerms): Builder
    {
        if (count($searchTerms) === 0) {
            return $query;
        }

        $column = $query->qualifyColumn($column);

        if ($like) {
            return $query->where(function (Builder $subQuery) use ($column, $searchTerms) {
                foreach ($searchTerms as $searchTerm) {
                    $subQuery->orWhere($column, 'LIKE', self::searchTermForLike($searchTerm));
                }

                return $subQuery;
            });
        }

        if (count($searchTerms) === 1) {
            return $query->where($column, '=', $searchTerms);
        }

        return $query->whereIn($column, $searchTerms);
    }

    protected function scopeSearch(Builder $query, string $column, bool $like, string ...$searchTerms): Builder
    {
        $searchTermsForExclude = [];
        $searchTermsForInclude = [];
        foreach ($searchTerms as $searchTerm) {
            if (trim($searchTerm) !== '') {
                if (str_starts_with($searchTerm, '-')) {
                    $searchTermsForExclude[] = substr($searchTerm, 1);
                } else {
                    $searchTermsForInclude[] = $searchTerm;
                }
            }
        }

        $query = $this->scopeExclude($query, $column, $like, ...$searchTermsForExclude);
        return $this->scopeInclude($query, $column, $like, ...$searchTermsForInclude);
    }

    private static function searchTermForLike(string $searchTerm): string
    {
        return '%' . trim(str_replace(' ', '%', $searchTerm)) . '%';
    }
}
