<?php

namespace Tests\Unit\Enums;

use App\Enums\FilterValue;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(FilterValue::class)]
class FilterValueTest extends TestCase
{
    #[DataProvider('castValues')]
    public function testCastToIntIfNoValue(string $value, int|string $expected): void
    {
        $cast = FilterValue::castToIntIfNoValue();

        self::assertSame($expected, $cast($value));
    }

    /**
     * @return list<array{string, int|string}>
     */
    public static function castValues(): array
    {
        return [
            ['42', 42],
            [FilterValue::All->value, FilterValue::All->value],
        ];
    }
}
