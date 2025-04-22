<?php

namespace App\Enums;

use App\Enums\Traits\NamedOption;
use App\GroupGenerationMethods\AgeBasedGroupGenerationMethod;
use App\GroupGenerationMethods\GeneratesGroups;
use App\GroupGenerationMethods\RandomizedAgeBasedGroupGenerationMethod;
use App\GroupGenerationMethods\RandomizedGroupGenerationMethod;
use App\Models\Booking;
use Illuminate\Database\Eloquent\Collection;

enum GroupGenerationMethod: string
{
    use NamedOption;

    case Randomized = 'randomized';
    case RandomizedAndAgeBased = 'randomized_age_based';
    case AgeBased = 'age_based';

    /**
     * @param  Collection<int, Booking> $bookings
     * @return array<int, Collection<int, Booking>>
     */
    public function generateGroups(int $groupsCount, Collection $bookings): array
    {
        return $this->getInstance()
            ->generateGroups($groupsCount, $bookings);
    }

    /**
     * @return class-string
     */
    private function getClass(): string
    {
        /**
         * Returned classes have to implement the {@see GeneratesGroups} interface.
         */
        return match ($this) {
            self::Randomized => RandomizedGroupGenerationMethod::class,
            self::RandomizedAndAgeBased => RandomizedAgeBasedGroupGenerationMethod::class,
            self::AgeBased => AgeBasedGroupGenerationMethod::class,
        };
    }

    private function getInstance(): GeneratesGroups
    {
        $className = $this->getClass();
        /** @phpstan-ignore return.type */
        return new $className();
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Randomized => __('randomized'),
            self::RandomizedAndAgeBased => __('randomized and age-based'),
            self::AgeBased => __('age-based'),
        };
    }
}
