<?php

namespace App\Http\Controllers\Traits;

use Closure;
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

    /**
     * @return string[]
     */
    protected function allowedIncludeCounts(): array
    {
        return [];
    }

    /**
     * @return array<int, Collection<int, AllowedInclude>>
     */
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

    /**
     * @return string[]
     */
    protected function allowedIncludeRelations(): array
    {
        return [];
    }

    /**
     * @return array<int, Collection<int, AllowedInclude>>
     */
    protected function allowedIncludeRelationsToSnake(): array
    {
        return array_map(
            static function (string $relationName) {
                /** @var Collection<int, AllowedInclude> $includesForRelation */
                $includesForRelation = AllowedInclude::relationship(
                    Str::snake($relationName),
                    $relationName
                );
                return $includesForRelation
                    ->filter(
                        /** @phpstan-ignore argument.type */
                        fn (AllowedInclude $allowedInclude) => !str_ends_with($allowedInclude->getName(), config('query-builder.count_suffix'))
                            /** @phpstan-ignore argument.type */
                            && !str_ends_with($allowedInclude->getName(), config('query-builder.exists_suffix'))
                    );
            },
            $this->allowedIncludeRelations()
        );
    }

    /**
     * @return array<int, Collection<int, AllowedInclude>>
     */
    protected function allowedIncludesToSnake(): array
    {
        return array_merge(
            $this->allowedIncludeRelationsToSnake(), // this includes count for the relations!
            $this->allowedIncludeCountsToSnake()
        );
    }

    /**
     * @template TModel of Model
     *
     * @param QueryBuilder<TModel> $query
     * @return LengthAwarePaginator<int, TModel>
     */
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
        if (!isset($include) || is_array($include)) {
            return;
        }

        $includeStrings = explode(',', $include);
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
            /** @phpstan-ignore property.dynamicName */
            $model->{$key} = $customInclude($model);
        }

        // Load missing includes.
        $model->loadMissing($builder->getEagerLoads());
    }
}
