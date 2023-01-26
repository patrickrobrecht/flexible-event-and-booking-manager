<?php

namespace App\Options;

use App\Options\Traits\NamedOption;

enum PaymentStatus: int
{
    use NamedOption;

    case Paid = 1;
    case NotPaid = 0;

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Paid => __('paid'),
            self::NotPaid => __('not paid yet'),
        };
    }
}
