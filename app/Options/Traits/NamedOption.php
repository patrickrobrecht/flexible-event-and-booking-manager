<?php

namespace App\Options\Traits;

use App\Options\FilterValue;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Portavice\Bladestrap\Support\Options;

trait NamedOption
{
    abstract public function getTranslatedName(): string;

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
