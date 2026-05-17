<?php

namespace App\Enums;

use App\Enums\Traits\NamedOption;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Location;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Model;

enum DocumentReferenceType: string
{
    use NamedOption;

    case Event = 'event';
    case EventSeries = 'series';
    case Location = 'location';
    case Organization = 'organization';

    /**
     * @return class-string<Model>
     */
    public function getClass(): string
    {
        return match ($this) {
            self::Event => Event::class,
            self::EventSeries => EventSeries::class,
            self::Location => Location::class,
            self::Organization => Organization::class,
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Event => __('Event'),
            self::EventSeries => __('Event series'),
            self::Location => __('Location'),
            self::Organization => __('Organization'),
        };
    }

    /**
     * @param array<class-string> $classes
     *
     * @return self[]
     */
    public static function casesVisibleForModels(array $classes): array
    {
        return self::casesFiltered(static fn (DocumentReferenceType $documentReferenceType) => in_array($documentReferenceType->getClass(), $classes, true));
    }
}
