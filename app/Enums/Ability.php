<?php

namespace App\Enums;

use App\Enums\Traits\NamedOption;
use Closure;

enum Ability: string
{
    use NamedOption;

    // Events
    case ViewEvents = 'events.view';
    case ViewPrivateEvents = 'events.view_private';
    case CreateEvents = 'events.create';
    case EditEvents = 'events.edit';
    case ViewResponsibilitiesOfEvents = 'events.responsibilities.view';
    case DestroyEvents = 'events.destroy';

    case ManageBookingOptionsOfEvent = 'events.manage_booking_options';
    case ViewBookingsOfEvent = 'events.view_bookings';
    case ExportBookingsOfEvent = 'events.export_bookings';
    case EditBookingsOfEvent = 'events.edit_bookings';
    case DeleteAndRestoreBookingsOfEvent = 'events.delete_and_restore_bookings';
    case EditBookingComment = 'events.edit_booking_comment';
    case ViewPaymentStatus = 'events.view_payment_status';
    case EditPaymentStatus = 'events.edit_payment_status';

    case ManageGroupsOfEvent = 'events.manage_groups';
    case ExportGroupsOfEvent = 'events.export_groups';

    case ViewEventSeries = 'event_series.view';
    case ViewPrivateEventSeries = 'event_series.view_private';
    case CreateEventSeries = 'event_series.create';
    case EditEventSeries = 'event_series.edit';
    case ViewResponsibilitiesOfEventSeries = 'event_series.responsibilities.view';
    case DestroyEventSeries = 'event_series.destroy';

    // Basic data
    case ViewOrganizations = 'organizations.view';
    case CreateOrganizations = 'organizations.create';
    case EditOrganizations = 'organizations.edit';
    case ViewResponsibilitiesOfOrganizations = 'organizations.responsibilities.view';
    case DestroyOrganizations = 'organizations.destroy';

    case ViewLocations = 'locations.view';
    case CreateLocations = 'locations.create';
    case EditLocations = 'locations.edit';
    case DestroyLocations = 'locations.destroy';

    // Documents
    case ViewDocumentsOfEvents = 'events.documents.view';
    case AddDocumentsToEvents = 'events.documents.create';
    case EditDocumentsOfEvents = 'events.documents.edit';
    case ViewCommentsOnDocumentsOfEvents = 'events.documents.comments.view';
    case CommentOnDocumentsOfEvents = 'events.documents.comments.create';
    case ChangeApprovalStatusOfDocumentsOfEvents = 'events.documents.approve';
    case DestroyDocumentsOfEvents = 'events.documents.destroy';

    case ViewDocumentsOfEventSeries = 'event_series.documents.view';
    case AddDocumentsToEventSeries = 'event_series.documents.create';
    case EditDocumentsOfEventSeries = 'event_series.documents.edit';
    case ViewCommentsOnDocumentsOfEventSeries = 'event_series.documents.comments.view';
    case CommentOnDocumentsOfEventSeries = 'event_series.documents.comments.create';
    case ChangeApprovalStatusOfDocumentsOfEventSeries = 'event_series.documents.approve';
    case DestroyDocumentsOfEventSeries = 'event_series.documents.destroy';

    case ViewDocumentsOfLocations = 'locations.documents.view';
    case AddDocumentsToLocations = 'locations.documents.create';
    case EditDocumentsOfLocations = 'locations.documents.edit';
    case ViewCommentsOnDocumentsOfLocations = 'locations.documents.comments.view';
    case CommentOnDocumentsOfLocations = 'locations.documents.comments.create';
    case ChangeApprovalStatusOfDocumentsOfLocations = 'locations.documents.approve';
    case DestroyDocumentsOfLocations = 'locations.documents.destroy';

    case ViewDocumentsOfOrganizations = 'organizations.documents.view';
    case AddDocumentsToOrganizations = 'organizations.documents.create';
    case EditDocumentsOfOrganizations = 'organizations.documents.edit';
    case ViewCommentsOnDocumentsOfOrganizations = 'organizations.documents.comments.view';
    case CommentOnDocumentsOfOrganizations = 'organizations.documents.comments.create';
    case ChangeApprovalStatusOfDocumentsOfOrganizations = 'organizations.documents.approve';
    case DestroyDocumentsOfOrganizations = 'organizations.documents.destroy';

    // Material
    case ViewMaterials = 'materials.view';
    case CreateMaterials = 'materials.create';
    case EditMaterials = 'materials.edit';
    case DestroyMaterials = 'materials.destroy';

    case ViewStorageLocations = 'storage_locations.view';
    case CreateStorageLocations = 'storage_locations.create';
    case EditStorageLocations = 'storage_locations.edit';
    case DestroyStorageLocations = 'storage_locations.destroy';

    // Users and abilities
    case ViewUsers = 'users.view';
    case CreateUsers = 'users.create';
    case EditUsers = 'users.edit';
    case DestroyUsers = 'users.destroy';

    case ViewUserRoles = 'user_roles.view';
    case CreateUserRoles = 'user_roles.create';
    case EditUserRoles = 'user_roles.edit';
    case DestroyUserRoles = 'user_roles.destroy';

    case ViewAccount = 'users.view_account';
    case ViewAbilities = 'users.view_account.abilities';
    case EditAccount = 'users.edit_account';

    case ViewApiDocumentation = 'api.docs.view';
    case ManagePersonalAccessTokens = 'personal_access_tokens.manage_own';

    // System management
    case ViewSystemInformation = 'system_info.view';

    public function dependsOnAbility(): ?self
    {
        return match ($this) {
            self::CreateEvents,
            self::EditEvents,
            self::ViewPrivateEvents,
            self::ViewResponsibilitiesOfEvents,
            self::ManageBookingOptionsOfEvent,
            self::DestroyEvents => self::ViewEvents,

            self::ExportBookingsOfEvent,
            self::EditBookingsOfEvent,
            self::DeleteAndRestoreBookingsOfEvent,
            self::EditBookingComment,
            self::ViewPaymentStatus => self::ViewBookingsOfEvent,
            self::EditPaymentStatus => self::ViewPaymentStatus,

            self::ExportGroupsOfEvent => self::ManageGroupsOfEvent,

            self::CreateEventSeries,
            self::EditEventSeries,
            self::ViewPrivateEventSeries,
            self::ViewResponsibilitiesOfEventSeries,
            self::DestroyEventSeries => self::ViewEventSeries,

            // Basic data
            self::CreateOrganizations,
            self::EditOrganizations,
            self::DestroyOrganizations => self::ViewOrganizations,

            self::CreateLocations,
            self::EditLocations,
            self::DestroyLocations => self::ViewLocations,

            // Documents
            self::AddDocumentsToEvents,
            self::EditDocumentsOfEvents,
            self::ViewCommentsOnDocumentsOfEvents,
            self::ChangeApprovalStatusOfDocumentsOfEvents,
            self::DestroyDocumentsOfEvents => self::ViewDocumentsOfEvents,
            self::CommentOnDocumentsOfEvents => self::ViewCommentsOnDocumentsOfEvents,

            self::AddDocumentsToEventSeries,
            self::EditDocumentsOfEventSeries,
            self::ViewCommentsOnDocumentsOfEventSeries,
            self::ChangeApprovalStatusOfDocumentsOfEventSeries,
            self::DestroyDocumentsOfEventSeries => self::ViewDocumentsOfEventSeries,
            self::CommentOnDocumentsOfEventSeries => self::ViewCommentsOnDocumentsOfEventSeries,

            self::AddDocumentsToLocations,
            self::EditDocumentsOfLocations,
            self::ViewCommentsOnDocumentsOfLocations,
            self::ChangeApprovalStatusOfDocumentsOfLocations,
            self::DestroyDocumentsOfLocations => self::ViewDocumentsOfLocations,
            self::CommentOnDocumentsOfLocations => self::ViewCommentsOnDocumentsOfLocations,

            self::AddDocumentsToOrganizations,
            self::EditDocumentsOfOrganizations,
            self::ViewCommentsOnDocumentsOfOrganizations,
            self::ChangeApprovalStatusOfDocumentsOfOrganizations,
            self::DestroyDocumentsOfOrganizations => self::ViewDocumentsOfOrganizations,
            self::CommentOnDocumentsOfOrganizations => self::ViewCommentsOnDocumentsOfOrganizations,

            // Materials
            self::CreateMaterials,
            self::EditMaterials,
            self::DestroyMaterials => self::ViewMaterials,

            self::CreateStorageLocations,
            self::EditStorageLocations,
            self::DestroyStorageLocations => self::ViewStorageLocations,

            // Users and abilities
            self::CreateUsers,
            self::EditUsers,
            self::DestroyUsers => self::ViewUsers,

            self::CreateUserRoles,
            self::EditUserRoles,
            self::DestroyUserRoles => self::ViewUserRoles,

            self::EditAccount => self::ViewAccount,

            self::ManagePersonalAccessTokens => self::ViewApiDocumentation,

            default => null,
        };
    }

    public function getAbilityGroup(): AbilityGroup
    {
        return match ($this) {
            // Events
            self::ViewEvents,
            self::ViewPrivateEvents,
            self::CreateEvents,
            self::EditEvents,
            self::ViewResponsibilitiesOfEvents,
            self::DestroyEvents => AbilityGroup::Events,
            self::ManageBookingOptionsOfEvent,
            self::ViewBookingsOfEvent,
            self::ExportBookingsOfEvent,
            self::EditBookingsOfEvent,
            self::DeleteAndRestoreBookingsOfEvent,
            self::EditBookingComment,
            self::ViewPaymentStatus,
            self::EditPaymentStatus => AbilityGroup::Bookings,
            self::ManageGroupsOfEvent,
            self::ExportGroupsOfEvent => AbilityGroup::Groups,
            self::ViewEventSeries,
            self::ViewPrivateEventSeries,
            self::CreateEventSeries,
            self::EditEventSeries,
            self::ViewResponsibilitiesOfEventSeries,
            self::DestroyEventSeries => AbilityGroup::EventSeries,

            // Basic data
            self::ViewOrganizations,
            self::CreateOrganizations,
            self::EditOrganizations,
            self::ViewResponsibilitiesOfOrganizations,
            self::DestroyOrganizations => AbilityGroup::Organizations,
            self::ViewLocations,
            self::CreateLocations,
            self::EditLocations,
            self::DestroyLocations => AbilityGroup::Locations,

            // Documents
            self::ViewDocumentsOfEvents,
            self::AddDocumentsToEvents,
            self::EditDocumentsOfEvents,
            self::ViewCommentsOnDocumentsOfEvents,
            self::CommentOnDocumentsOfEvents,
            self::ChangeApprovalStatusOfDocumentsOfEvents,
            self::DestroyDocumentsOfEvents => AbilityGroup::DocumentsOfEvents,
            self::ViewDocumentsOfEventSeries,
            self::AddDocumentsToEventSeries,
            self::EditDocumentsOfEventSeries,
            self::ViewCommentsOnDocumentsOfEventSeries,
            self::CommentOnDocumentsOfEventSeries,
            self::ChangeApprovalStatusOfDocumentsOfEventSeries,
            self::DestroyDocumentsOfEventSeries => AbilityGroup::DocumentsOfEventSeries,
            self::ViewDocumentsOfLocations,
            self::AddDocumentsToLocations,
            self::EditDocumentsOfLocations,
            self::ViewCommentsOnDocumentsOfLocations,
            self::CommentOnDocumentsOfLocations,
            self::ChangeApprovalStatusOfDocumentsOfLocations,
            self::DestroyDocumentsOfLocations => AbilityGroup::DocumentsOfLocations,
            self::ViewDocumentsOfOrganizations,
            self::AddDocumentsToOrganizations,
            self::EditDocumentsOfOrganizations,
            self::ViewCommentsOnDocumentsOfOrganizations,
            self::CommentOnDocumentsOfOrganizations,
            self::ChangeApprovalStatusOfDocumentsOfOrganizations,
            self::DestroyDocumentsOfOrganizations => AbilityGroup::DocumentsOfOrganizations,

            // Materials
            self::ViewMaterials,
            self::CreateMaterials,
            self::EditMaterials,
            self::DestroyMaterials => AbilityGroup::Materials,

            self::ViewStorageLocations,
            self::CreateStorageLocations,
            self::EditStorageLocations,
            self::DestroyStorageLocations => AbilityGroup::StorageLocations,

            // Users and abilities
            self::ViewUsers,
            self::CreateUsers,
            self::EditUsers,
            self::DestroyUsers => AbilityGroup::Users,
            self::ViewUserRoles,
            self::CreateUserRoles,
            self::EditUserRoles,
            self::DestroyUserRoles => AbilityGroup::UserRoles,
            self::ViewAccount,
            self::ViewAbilities,
            self::EditAccount => AbilityGroup::OwnAccount,
            self::ViewApiDocumentation,
            self::ManagePersonalAccessTokens => AbilityGroup::ApiAccess,

            // System management
            self::ViewSystemInformation => AbilityGroup::SystemManagement,
        };
    }

    public function getTranslatedName(): string
    {
        return match ($this) {
            // Events
            self::ViewEvents => __('View events'),
            self::ViewPrivateEvents => __('View private events'),
            self::CreateEvents => __('Create events'),
            self::EditEvents => __('Edit events'),
            self::ViewResponsibilitiesOfEvents => __('View responsibilities of events'),
            self::DestroyEvents => __('Delete events permanently'),

            self::ManageBookingOptionsOfEvent => __('Manage booking options of event'),
            self::ViewBookingsOfEvent => __('View bookings of event'),
            self::ExportBookingsOfEvent => __('Export bookings of event'),
            self::EditBookingsOfEvent => __('Edit bookings of event'),
            self::DeleteAndRestoreBookingsOfEvent => __('Delete and restore bookings of event'),
            self::EditBookingComment => __('Edit booking comment'),
            self::ViewPaymentStatus => __('View payment status'),
            self::EditPaymentStatus => __('Edit payment status'),

            self::ManageGroupsOfEvent => __('Manage groups of event'),
            self::ExportGroupsOfEvent => __('Export groups of event'),

            self::ViewEventSeries => __('View event series'),
            self::ViewPrivateEventSeries => __('View private event series'),
            self::CreateEventSeries => __('Create event series'),
            self::EditEventSeries => __('Edit event series'),
            self::ViewResponsibilitiesOfEventSeries => __('View responsibilities of event series'),
            self::DestroyEventSeries => __('Delete event series permanently'),

            // Basic data
            self::ViewOrganizations => __('View organizations'),
            self::CreateOrganizations => __('Create organizations'),
            self::EditOrganizations => __('Edit organizations'),
            self::ViewResponsibilitiesOfOrganizations => __('View responsibilities of organizations'),
            self::DestroyOrganizations => __('Delete organizations permanently'),

            self::ViewLocations => __('View locations'),
            self::CreateLocations => __('Create locations'),
            self::EditLocations => __('Edit locations'),
            self::DestroyLocations => __('Delete locations permanently'),

            // Documents
            self::ViewDocumentsOfEvents,
            self::ViewDocumentsOfEventSeries,
            self::ViewDocumentsOfLocations,
            self::ViewDocumentsOfOrganizations => __('View documents'),
            self::AddDocumentsToEvents,
            self::AddDocumentsToEventSeries,
            self::AddDocumentsToLocations,
            self::AddDocumentsToOrganizations => __('Add documents'),
            self::EditDocumentsOfEvents,
            self::EditDocumentsOfEventSeries,
            self::EditDocumentsOfLocations,
            self::EditDocumentsOfOrganizations => __('Edit documents'),
            self::ViewCommentsOnDocumentsOfEvents,
            self::ViewCommentsOnDocumentsOfEventSeries,
            self::ViewCommentsOnDocumentsOfLocations,
            self::ViewCommentsOnDocumentsOfOrganizations => __('View comments on documents'),
            self::CommentOnDocumentsOfEvents,
            self::CommentOnDocumentsOfEventSeries,
            self::CommentOnDocumentsOfLocations,
            self::CommentOnDocumentsOfOrganizations => __('Comment on documents'),
            self::ChangeApprovalStatusOfDocumentsOfEvents,
            self::ChangeApprovalStatusOfDocumentsOfOrganizations,
            self::ChangeApprovalStatusOfDocumentsOfLocations,
            self::ChangeApprovalStatusOfDocumentsOfEventSeries => __('Change approval status of documents'),
            self::DestroyDocumentsOfEvents,
            self::DestroyDocumentsOfEventSeries,
            self::DestroyDocumentsOfLocations,
            self::DestroyDocumentsOfOrganizations => __('Delete documents permanently'),

            // Materials
            self::ViewMaterials => __('View materials'),
            self::CreateMaterials => __('Create materials'),
            self::EditMaterials => __('Edit materials'),
            self::DestroyMaterials => __('Delete materials permanently'),

            self::ViewStorageLocations => __('View storage locations'),
            self::CreateStorageLocations => __('Create storage locations'),
            self::EditStorageLocations => __('Edit storage locations'),
            self::DestroyStorageLocations => __('Delete storage locations permanently'),

            // Users and abilities
            self::ViewUsers => __('View users'),
            self::CreateUsers => __('Create users'),
            self::EditUsers => __('Edit users'),
            self::DestroyUsers => __('Delete users permanently'),

            self::ViewUserRoles => __('View user roles'),
            self::CreateUserRoles => __('Create user roles'),
            self::EditUserRoles => __('Edit user roles'),
            self::DestroyUserRoles => __('Delete user roles permanently'),

            self::ViewAccount => __('View own account'),
            self::ViewAbilities => __('View abilities'),
            self::EditAccount => __('Edit own account'),

            self::ManagePersonalAccessTokens => __('Manage personal access tokens'),
            self::ViewApiDocumentation => __('View API documentation'),

            self::ViewSystemInformation => __('View system information'),
        };
    }

    /**
     * @return self[]
     */
    public static function apiCases(): array
    {
        return [
            self::ViewEvents,
            self::ViewPrivateEvents,
            self::ViewEventSeries,
            self::ViewPrivateEventSeries,
            self::ViewOrganizations,
            self::ViewLocations,
        ];
    }

    /**
     * @param static|static[] $exceptions
     *
     * @return static[]
     */
    public static function apiCasesExcept(array|self $exceptions): array
    {
        $exceptionValues = is_array($exceptions)
            ? array_map(static fn (self $case) => $case->value, $exceptions)
            : [$exceptions->value];

        return self::apiCasesFiltered(static fn (self $case) => !in_array($case->value, $exceptionValues, true));
    }

    /**
     * @param Closure(static): bool $closure
     *
     * @return static[]
     */
    public static function apiCasesFiltered(Closure $closure): array
    {
        return array_filter(self::apiCases(), static fn (self $case) => $closure($case));
    }
}
