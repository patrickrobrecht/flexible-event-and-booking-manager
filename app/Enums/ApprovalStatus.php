<?php

namespace App\Enums;

use App\Enums\Traits\NamedOption;

enum ApprovalStatus: int
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
        return match ($this) {
            self::WaitingForApproval, self::UnderReview => 'fa fa-fw fa-question',
            self::Approved => 'fa fa-fw fa-thumbs-up',
            self::ChangesRequested => 'fa fa-fw fa-thumbs-down',
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
