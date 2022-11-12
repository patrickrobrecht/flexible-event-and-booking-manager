<?php

namespace App\Options\Traits;

use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\In;

trait NamedOption
{
    abstract public function getTranslatedName(): string;

    public static function keysWithNames(): array
    {
        $all = [];
        foreach (self::cases() as $enumCase) {
            $all[$enumCase->value ?? $enumCase->name] = $enumCase->getTranslatedName();
        }

        return $all;
    }

    public static function keysWithNamesAndAll(): array
    {
        return array_replace(
            [
                '' => __('all'),
            ],
            self::keysWithNames()
        );
    }

    public static function exists(string|int $key): bool
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
}
