<?php

namespace App\Enums;

use App\Enums\Interfaces\MakesBadges;
use App\Enums\Traits\NamedOption;

enum BookingStatus: int implements MakesBadges
{
    use NamedOption;

    case Confirmed = 1;
    case Waiting = 2;

    public function getBadgeVariant(): string
    {
        return match ($this) {
            self::Confirmed => 'success',
            self::Waiting => 'warning',
        };
    }

    public function getIcon(): string
    {
        return 'fa fa-fw ' . match ($this) {
            self::Confirmed => 'fa-check',
            self::Waiting => 'fa-question',
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Confirmed => __('confirmed'),
            self::Waiting => __('on the waiting list'),
        };
    }
}
