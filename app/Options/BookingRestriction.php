<?php

namespace App\Options;

use App\Options\Traits\NamedOption;

enum BookingRestriction : string
{
    use NamedOption;

    case AccountRequired = 'require_account';
    case VerifiedEmailAddressRequired = 'require_verified_email_address';
    case OnlySelf = 'only_self';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::AccountRequired => __('account required'),
            self::VerifiedEmailAddressRequired => __('verified email address required'),
            self::OnlySelf => __('only self'),
        };
    }
}
