<?php

namespace App\Options;

use App\Options\Traits\NamedOption;

enum Visibility: string
{
    use NamedOption;

    case Private = 'private';
    case Public = 'public';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Public => __('public'),
            self::Private => __('private'),
        };
    }
}
