<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\AllowedInclude;
use Spatie\QueryBuilder\QueryBuilder;

trait SupportsIncludesInSnakeCase
{
    /**
     * @return array<string, Closure>
     */
    protected function allowedCustomIncludes(): array
    {
        return [];
    }

    protected function allowedIncludeCounts(): array
    {
        return [];
    }

    protected function allowedIncludeCountsToSnake(): array
    {
        return array_map(
            static function (string $relationName) {
                return AllowedInclude::count(
                    Str::snake($relationName . 'Count'),
                    $relationName
                );
            },
            $this->allowedIncludeCounts()
        );
    }

    protected function allowedIncludeRelations(): array
    {
        return [];
    }

    protected function allowedIncludeRelationsToSnake(): array
    {
        return array_map(
            static function (string $relationName) {
                $includesForRelation = AllowedInclude::relationship(
                    Str::snake($relationName),
                    $relationName
                );
                return $includesForRelation
                    ->filter(
                        fn (AllowedInclude $allowedInclude) => !str_ends_with($allowedInclude->getName(), config('query-builder.count_suffix'))
                            && !str_ends_with($allowedInclude->getName(), config('query-builder.exists_suffix'))
                    );
            },
            $this->allowedIncludeRelations()
        );
    }

    protected function allowedIncludesToSnake(): array
    {
        return array_merge(
            $this->allowedIncludeRelationsToSnake(), // this includes count for the relations!
            $this->allowedIncludeCountsToSnake()
        );
    }

    protected function loadPaginatedListWithIncludes(FormRequest $request, QueryBuilder $query): LengthAwarePaginator
    {
        return $query
            ->allowedIncludes($this->allowedIncludesToSnake())
            ->paginate()
            ->appends($request->query());
    }

    protected function loadIncludesForModel(Model $model): void
    {
        $include = Request::query('include');
        if (!isset($include)) {
            return;
        }

        $includeStrings = explode(',', $include);
        if (count($includeStrings) === 0) {
            return;
        }

        $allowedCustomIncludes = Collection::make($this->allowedCustomIncludes());
        $customIncludes = $allowedCustomIncludes->filter(
            static fn ($value, $key) => in_array($key, $includeStrings, true)
        );
        Request::merge([
            'include' => Collection::make($includeStrings)->diff($customIncludes->keys())->join(','),
        ]);

        $builder = QueryBuilder::for($model::class)
            ->allowedIncludes(
                Collection::make($this->allowedIncludeRelationsToSnake())
                    ->flatten()
                    ->filter(
                        // Don't allow _count includes here.
                        static fn (AllowedInclude $allowedInclude) => !str_ends_with($allowedInclude->getName(), '_count')
                    )
            );

        // Load custom includes.
        foreach ($customIncludes as $key => $customInclude) {
            $model->{$key} = $customInclude($model);
        }

        // Load missing includes.
        $model->loadMissing($builder->getEagerLoads());
    }
}
