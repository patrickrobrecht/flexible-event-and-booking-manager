<?php

namespace App\Options\Traits;

use App\Options\FilterValue;
use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Portavice\Bladestrap\Support\Options;

trait NamedOption
{
    abstract public function getTranslatedName(): string;

    /**
     * @param static|static[] $exceptions
     *
     * @return static[]
     */
    public static function casesExcept(array|self $exceptions): array
    {
        $exceptionValues = is_array($exceptions)
            ? array_map(fn (self $case) => $case->value, $exceptions)
            : [$exceptions->value];

        return self::casesFiltered(fn (self $case) => in_array($case, $exceptionValues, true));
    }

    /**
     * @param Closure(static): bool $closure
     *
     * @return static[]
     */
    public static function casesFiltered(Closure $closure): array
    {
        return array_filter(self::cases(), fn (self $case) => $closure($case));
    }

    public static function exists(int|string $value): bool
    {
        return self::tryFrom($value) !== null;
    }

    public static function values(?array $array = null): array
    {
        return array_map(
            static fn ($enumCase) => $enumCase->value ?? $enumCase->name,
            $array ?? self::cases()
        );
    }

    public static function rule(?array $array = null): In
    {
        return Rule::in(self::values($array));
    }

    public static function toOptions(): Options
    {
        return Options::fromEnum(static::class, 'getTranslatedName');
    }

    public static function toOptionsWithAll(): Options
    {
        return self::toOptions()->prepend(__('all'), FilterValue::All->value);
    }
}
