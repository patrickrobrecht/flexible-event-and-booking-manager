<?php

namespace App\Options;

enum AbilityGroup
{
    case Events;
    case Bookings;
    case Groups;
    case EventSeries;

    case BasicData;
    case Organizations;
    case Locations;

    case Documents;
    case DocumentsOfEvents;
    case DocumentsOfEventSeries;
    case DocumentsOfOrganizations;

    case UsersAndAbilities;
    case Users;
    case UserRoles;
    case OwnAccount;

    /**
     * @return Ability[]
     */
    public function getAbilities(): array
    {
        return array_filter(Ability::cases(), fn (Ability $ability) => $ability->getAbilityGroup() === $this);
    }

    /**
     * @return self[]
     */
    public function getChildren(): array
    {
        return array_filter(self::cases(), fn (self $abilityGroup) => $abilityGroup->getParent() === $this);
    }

    public function getParent(): ?self
    {
        return match ($this) {
            self::Bookings,
            self::Groups,
            self::EventSeries => self::Events,

            self::Organizations,
            self::Locations => self::BasicData,

            self::DocumentsOfEvents,
            self::DocumentsOfEventSeries,
            self::DocumentsOfOrganizations => self::Documents,

            self::Users,
            self::UserRoles,
            self::OwnAccount => self::UsersAndAbilities,

            default => null,
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Events => __('Events'),
            self::Bookings => __('Bookings'),
            self::Groups => __('Groups'),

            self::EventSeries => __('Event series'),

            self::BasicData => __('Basic data'),
            self::Organizations => __('Organizations'),
            self::Locations => __('Locations'),

            self::Documents => __('Documents'),
            self::DocumentsOfEvents => __('Documents of events'),
            self::DocumentsOfEventSeries => __('Documents of event series'),
            self::DocumentsOfOrganizations => __('Documents of organizations'),

            self::UsersAndAbilities => __('Users and abilities'),
            self::Users => __('Users'),
            self::UserRoles => __('User roles'),
            self::OwnAccount => __('Own account'),
        };
    }

    public function hasChildren(): bool
    {
        return count($this->getChildren()) > 0;
    }

    /**
     * @return self[]
     */
    public static function casesAtRootLevel(): array
    {
        return array_filter(self::cases(), fn (self $abilityGroup) => $abilityGroup->getParent() === null);
    }
}
