<?php

namespace App\Enums;

use App\Enums\Traits\NamedOption;
use Spatie\QueryBuilder\Filters\FiltersTrashed;

/**
 * Options to display {@see FiltersTrashed}
 */
enum DeletedFilter: string
{
    use NamedOption;

    case HideDeleted = '';
    case ShowDeleted = 'with';
    case OnlyDeleted = 'only';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::HideDeleted => __('hide deleted'),
            self::ShowDeleted => __('show deleted'),
            self::OnlyDeleted => __('show only deleted'),
        };
    }
}
