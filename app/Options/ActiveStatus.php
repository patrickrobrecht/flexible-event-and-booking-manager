<?php

namespace App\Options;

use App\Options\Traits\NamedOption;

enum ActiveStatus: int
{
    use NamedOption;

    case Active = 1;
    case Inactive = 0;
    case Archived = 2;

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Active => __('active'),
            self::Inactive => __('inactive'),
            self::Archived => __('archived'),
        };
    }
}
