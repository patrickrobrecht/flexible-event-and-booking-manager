<?php

namespace App\Enums;

use App\Enums\Interfaces\MakesBadges;
use App\Enums\Traits\NamedOption;

enum MaterialStatus: int implements MakesBadges
{
    use NamedOption;

    case Missing = 0;
    case Checked = 1;
    case InProcess = 2;
    case LentOut = 3;

    public function getBadgeVariant(): string
    {
        return match ($this) {
            self::Missing => 'danger',
            self::Checked => 'success',
            self::InProcess => 'warning',
            self::LentOut => 'secondary',
        };
    }

    public function getIcon(): string
    {
        return 'fa fa-fw ' . match ($this) {
            self::Missing => 'fa-circle-exclamation',
            self::Checked => 'fa-check-circle',
            self::InProcess => 'fa-spinner',
            self::LentOut => 'fa-arrow-right-arrow-left',
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Missing => __('missing'),
            self::Checked => __('checked'),
            self::InProcess => __('in process'),
            self::LentOut => __('lent out'),
        };
    }
}
