<?php

namespace App\Enums\Traits;

use App\Enums\FilterValue;
use Closure;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Portavice\Bladestrap\Support\Options;
use UnitEnum;

/**
 * @phpstan-require-implements UnitEnum
 */
trait NamedOption
{
    /**
     * @return string|value-of<static>
     */
    private function valueOrName(): int|string
    {
        /** @phpstan-ignore-next-line property.notFound */
        return $this->value ?? $this->name;
    }

    /**
     * @param static|static[] $exceptions
     *
     * @return static[]
     */
    public static function casesExcept(array|self $exceptions): array
    {
        $exceptionValues = is_array($exceptions)
            ? array_map(static fn (self $case) => $case->valueOrName(), $exceptions)
            : [$exceptions->valueOrName()];

        return self::casesFiltered(static fn (self $case) => !in_array($case->valueOrName(), $exceptionValues, true));
    }

    /**
     * @param Closure(static): bool $closure
     *
     * @return static[]
     */
    public static function casesFiltered(Closure $closure): array
    {
        return array_filter(self::cases(), static fn (self $case) => $closure($case));
    }

    public static function exists(int|string $value): bool
    {
        /** @phpstan-ignore-next-line staticMethod.notFound */
        return self::tryFrom($value) !== null;
    }

    abstract public function getTranslatedName(): string;

    /**
     * @param self[]|null $array
     */
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

    /**
     * @param self[]|null $array
     *
     * @return array<int, string|value-of<static>>
     */
    public static function values(?array $array = null): array
    {
        /** @phpstan-ignore-next-line return.type */
        return array_map(
            static fn (self $enumCase) => $enumCase->valueOrName(),
            $array ?? self::cases()
        );
    }
}
