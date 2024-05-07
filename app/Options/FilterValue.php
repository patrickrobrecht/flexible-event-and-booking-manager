<?php

namespace App\Options;

use App\Options\Traits\NamedOption;

enum FilterValue: string
{
    use NamedOption;

    case All = '*';
    case With = '+';
    case Without = '-';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::All => __('all'),
            self::With => __('with at least one value'),
            self::Without => __('without values'),
        };
    }

    public static function castToIntIfNoValue(): \Closure
    {
        return static fn ($v) => in_array($v, self::values(), true) ? $v : (int) $v;
    }
}
