<?php

namespace App\Enums;

use App\Enums\Interfaces\MakesBadges;
use App\Enums\Traits\NamedOption;

enum ApprovalStatus: int implements MakesBadges
{
    use NamedOption;

    case WaitingForApproval = 0;
    case UnderReview = 3;
    case Approved = 1;
    case ChangesRequested = 2;

    public function getBadgeVariant(): string
    {
        return match ($this) {
            self::WaitingForApproval, => 'dark',
            self::UnderReview => 'warning',
            self::Approved => 'success',
            self::ChangesRequested => 'danger',
        };
    }

    public function getIcon(): string
    {
        return 'fa fa-fw ' . match ($this) {
            self::WaitingForApproval, self::UnderReview => 'fa-question',
            self::Approved => 'fa-thumbs-up',
            self::ChangesRequested => 'fa-thumbs-down',
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::WaitingForApproval => __('waiting for approval'),
            self::UnderReview => __('under review'),
            self::Approved => __('approved'),
            self::ChangesRequested => __('changes requested'),
        };
    }
}
