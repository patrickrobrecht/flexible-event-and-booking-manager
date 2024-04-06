<?php

namespace App\Options;

use App\Options\Traits\NamedOption;

enum EventSeriesType: string
{
    use NamedOption;

    case MainEventSeries = 'main_event_series';
    case PartOfEventSeries = 'part_of_event_series';
    case EventSeriesWithParts = 'event_with_part_series';
    case EventSeriesWithoutParts = 'event_without_part_series';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::MainEventSeries => __('main event series'),
            self::PartOfEventSeries => __('part of another event series'),
            self::EventSeriesWithParts => __('event series with part event series'),
            self::EventSeriesWithoutParts => __('event series without part event series'),
        };
    }
}
