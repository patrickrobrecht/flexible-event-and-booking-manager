<?php

namespace App\Enums;

use App\Enums\Traits\NamedOption;

enum EventType: string
{
    use NamedOption;

    case MainEvent = 'main_event';
    case PartOfEvent = 'part_of_event';
    case EventWithParts = 'event_with_parts';
    case EventWithoutParts = 'event_without_parts';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::MainEvent => __('main event'),
            self::PartOfEvent => __('part of another event'),
            self::EventWithParts => __('event with part events'),
            self::EventWithoutParts => __('event without part events'),
        };
    }
}
