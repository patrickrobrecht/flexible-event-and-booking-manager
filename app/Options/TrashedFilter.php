<?php

namespace App\Options;

use App\Options\Traits\NamedOption;

enum TrashedFilter: string
{
    use NamedOption;

    case WithoutTrashed = '';
    case WithTrashed = 'with';
    case OnlyTrashed = 'only';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::WithoutTrashed => __('without trashed'),
            self::WithTrashed => __('with trashed'),
            self::OnlyTrashed => __('only trashed'),
        };
    }
}
