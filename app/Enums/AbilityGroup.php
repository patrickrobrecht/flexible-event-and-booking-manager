<?php

namespace App\Enums;

enum AbilityGroup
{
    case Events;
    case Bookings;
    case Groups;
    case EventSeries;

    case Organizations;

    case Locations;

    case Documents;
    case DocumentsOfEvents;
    case DocumentsOfEventSeries;
    case DocumentsOfOrganizations;

    case Materials;
    case StorageLocations;

    case UsersAndAbilities;
    case Users;
    case UserRoles;
    case OwnAccount;
    case ApiAccess;

    case SystemManagement;

    /**
     * @param Ability[] $abilities
     *
     * @return Ability[]
     */
    public function filterAbilities(array $abilities): array
    {
        return array_filter($abilities, fn (Ability $ability) => $ability->getAbilityGroup() === $this);
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

            self::DocumentsOfEvents,
            self::DocumentsOfEventSeries,
            self::DocumentsOfOrganizations => self::Documents,

            self::Users,
            self::UserRoles,
            self::OwnAccount,
            self::ApiAccess => self::UsersAndAbilities,

            default => null,
        };
    }

    public function getIcon(): string
    {
        return 'fa fa-fw ' . match ($this) {
            self::Events => 'fa-calendar-days',
            self::Bookings => 'fa-file-contract',
            self::Groups => 'fa-people-group',

            self::EventSeries => 'fa-calendar-week',

            self::Organizations => 'fa-sitemap',

            self::Locations => 'fa-location-pin',

            self::Documents,
            self::DocumentsOfEvents,
            self::DocumentsOfEventSeries,
            self::DocumentsOfOrganizations => 'fa-file',

            self::Materials => 'fa-toolbox',
            self::StorageLocations => 'fa-warehouse',

            self::UsersAndAbilities,
            self::Users => 'fa-users',
            self::UserRoles => 'fa-user-group',
            self::OwnAccount => 'fa-user-circle',
            self::ApiAccess => 'fa-file-code',

            self::SystemManagement => 'fa-cog',
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::Events => __('Events'),
            self::Bookings => __('Bookings'),
            self::Groups => __('Groups'),

            self::EventSeries => __('Event series'),

            self::Organizations => __('Organizations'),

            self::Locations => __('Locations'),

            self::Documents => __('Documents'),
            self::DocumentsOfEvents => __('Documents of events'),
            self::DocumentsOfEventSeries => __('Documents of event series'),
            self::DocumentsOfOrganizations => __('Documents of organizations'),

            self::Materials => __('Materials'),
            self::StorageLocations => __('Storage locations'),

            self::UsersAndAbilities => __('Users and abilities'),
            self::Users => __('Users'),
            self::UserRoles => __('User roles'),
            self::OwnAccount => __('Own account'),
            self::ApiAccess => __('Access to the REST API'),

            self::SystemManagement => __('System management'),
        };
    }

    /**
     * Checks whether one of the child groups contains at least one of the given abilities.
     *
     * @param Ability[] $abilities
     */
    public function hasChildrenWithAbilities(array $abilities): bool
    {
        foreach ($this->getChildren() as $childGroup) {
            if (count($childGroup->filterAbilities($abilities)) > 0) {
                return true;
            }

            if ($childGroup->hasChildrenWithAbilities($abilities)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return self[]
     */
    public static function casesAtRootLevel(): array
    {
        return array_filter(self::cases(), fn (self $abilityGroup) => $abilityGroup->getParent() === null);
    }
}
