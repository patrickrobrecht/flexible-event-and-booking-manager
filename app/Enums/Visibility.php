<?php

namespace App\Enums;

use App\Enums\Interfaces\MakesBadges;
use App\Enums\Traits\NamedOption;

enum Visibility: string implements MakesBadges
{
    use NamedOption;

    case Private = 'private';
    case Public = 'public';

    public function getBadgeVariant(): string
    {
        return match ($this) {
            self::Private => 'danger',
            self::Public => 'success',
        };
    }

    public function getIcon(): string
    {
        return 'fa fa-fw ' . match ($this) {
            self::Private => 'fa-lock',
            self::Public => 'fa-lock-open',
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Private => __('private'),
            self::Public => __('public'),
        };
    }
}
