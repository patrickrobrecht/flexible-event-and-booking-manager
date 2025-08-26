<?php

namespace App\Enums\Interfaces;

interface MakesBadges
{
    public function getBadgeVariant(): string;

    public function getIcon(): string;

    public function getTranslatedName(): string;
}
