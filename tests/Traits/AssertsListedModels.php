<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use Symfony\Component\HttpFoundation\Response;

/**
 * @mixin Assert
 */
trait AssertsListedModels
{
    /**
     * Request the list with the filter and assert that exactly the expected models are listed.
     *
     * @param string $filter Filter query, placeholders like {name} are replaced by the ID of the model with that name.
     * @param array<string, Model> $models Named models, used for placeholders and expected models.
     * @param list<string> $expectedModelNames
     * @param Model[] $ignoredModels Models which are not relevant for the filter, e.g. the acting user, and therefore ignored in the list.
     */
    protected function assertFilteredList(
        string $route,
        string $filter,
        string $viewKey,
        array $models,
        array $expectedModelNames,
        array $ignoredModels = []
    ): void {
        $filter = preg_replace_callback(
            '/{(\w+)}/',
            static function (array $matches) use ($models): string {
                $key = $models[$matches[1]]->getKey();
                self::assertIsInt($key);

                return (string) $key;
            },
            $filter
        );

        self::assertListedModels(
            $this->get("{$route}?{$filter}")->assertOk(),
            $viewKey,
            array_values(Arr::only($models, $expectedModelNames)),
            "Unexpected models listed for {$filter}",
            $ignoredModels
        );
    }

    /**
     * Assert that the paginated list passed to the view contains exactly the expected models (in any order).
     *
     * @param TestResponse<Response> $response
     * @param Model[] $expectedModels
     * @param Model[] $ignoredModels Models ignored in the list.
     */
    protected static function assertListedModels(
        TestResponse $response,
        string $viewKey,
        array $expectedModels,
        string $message = '',
        array $ignoredModels = []
    ): void {
        $paginator = $response->viewData($viewKey);
        self::assertInstanceOf(AbstractPaginator::class, $paginator);

        $toSortedIds = static fn (iterable $models) => Collection::make($models)
            ->pluck('id')
            ->diff(Collection::make($ignoredModels)->pluck('id'))
            ->sort()
            ->values()
            ->all();
        self::assertSame($toSortedIds($expectedModels), $toSortedIds($paginator->items()), $message);
    }
}
