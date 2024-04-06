<?php

namespace App\Options;

use App\Options\Traits\NamedOption;

enum ApprovalStatus: int
{
    use NamedOption;

    case WaitingForApproval = 0;
    case Approved = 1;
    case ChangesRequested = 2;

    public function getBadgeVariant(): string
    {
        return match ($this) {
            self::WaitingForApproval => 'warning',
            self::Approved => 'success',
            self::ChangesRequested => 'danger',
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::WaitingForApproval => 'fa fa-fw fa-question',
            self::Approved => 'fa fa-fw fa-thumbs-up',
            self::ChangesRequested => 'fa fa-fw fa-thumbs-down',
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::WaitingForApproval => __('waiting for approval'),
            self::Approved => __('approved'),
            self::ChangesRequested => __('changes requested'),
        };
    }
}
