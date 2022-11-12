<?php

namespace App\Helpers;

use Illuminate\Support\Arr;

/**
 * Helper class to populate GET parameters into forms similar to the old() function.
 *
 * Use {@see QueryInput::setDefaults()} to provide default values for parameters not present in the query.
 */
class QueryInput
{
    private static array $defaults = [];

    public static function setDefaults(array $defaults): void
    {
        self::$defaults = $defaults;
    }

    public static function all(): array
    {
        return app('request')->query();
    }

    public static function allWithDefaults(): array
    {
        return array_replace(
            self::$defaults,
            self::all()
        );
    }

    public static function hasAny(): bool
    {
        return count(self::all()) > 0;
    }

    public static function hasAnyOrDefault(): bool
    {
        return count(self::allWithDefaults()) > 0;
    }

    public static function old(?string $key = null, mixed $default = null): mixed
    {
        $request = app('request');

        if (is_null($key)) {
            return self::allWithDefaults();
        }

        return Arr::get(
            $request->query(),
            $key,
            Arr::get(
                self::$defaults,
                $key,
                $default
            )
        );
    }
}
