<?php

namespace App\Enums;

use App\Enums\Interfaces\MakesBadges;
use App\Enums\Traits\NamedOption;

enum ActiveStatus: int implements MakesBadges
{
    use NamedOption;

    case Active = 1;
    case Inactive = 0;
    case Archived = 2;

    public function getBadgeVariant(): string
    {
        return match ($this) {
            self::Active => 'success',
            self::Inactive => 'danger',
            self::Archived => 'dark',
        };
    }

    public function getIcon(): string
    {
        return 'fa fw ' . match ($this) {
            self::Active => 'fa-check-circle',
            self::Inactive => 'fa-power-off',
            self::Archived => 'fa-archive',
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Active => __('active'),
            self::Inactive => __('inactive'),
            self::Archived => __('archived'),
        };
    }
}
