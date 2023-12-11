<?php

namespace App\Options\Traits;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;
use Portavice\Bladestrap\Support\Options;

trait NamedOption
{
    abstract public function getTranslatedName(): string;

    public static function exists(int|string $key): bool
    {
        return in_array($key, self::keys(), true);
    }

    public static function keys(): array
    {
        return array_map(
            static fn ($enumCase) => $enumCase->value ?? $enumCase->name,
            self::cases()
        );
    }

    public static function rule(): In
    {
        return Rule::in(self::keys());
    }

    public static function toOptions(): Options
    {
        return Options::fromEnum(static::class, 'getTranslatedName');
    }

    public static function toOptionsWithAll(): Options
    {
        return self::toOptions()->prepend(__('all'), '');
    }
}
