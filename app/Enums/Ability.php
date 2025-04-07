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

    // Basic data
    case ViewOrganizations = 'organizations.view';
    case CreateOrganizations = 'organizations.create';
    case EditOrganizations = 'organizations.edit';
    case ViewResponsibilitiesOfOrganizations = 'organizations.responsibilities.view';

    case ViewLocations = 'locations.view';
    case CreateLocations = 'locations.create';
    case EditLocations = 'locations.edit';

    // Documents
    case ViewDocuments = 'documents.view';
    case ViewCommentsOnDocuments = 'documents.comments.view';
    case CommentOnDocuments = 'documents.comments.create';
    case ChangeApprovalStatusOfDocuments = 'documents.approve';

    case ViewDocumentsOfEvents = 'events.documents.view';
    case AddDocumentsToEvents = 'events.documents.create';
    case EditDocumentsOfEvents = 'events.documents.edit';
    case DeleteDocumentsOfEvents = 'events.documents.delete';

    case ViewDocumentsOfEventSeries = 'event_series.documents.view';
    case AddDocumentsToEventSeries = 'event_series.documents.create';
    case EditDocumentsOfEventSeries = 'event_series.documents.edit';
    case DeleteDocumentsOfEventSeries = 'event_series.documents.delete';

    case ViewDocumentsOfOrganizations = 'organizations.documents.view';
    case AddDocumentsToOrganizations = 'organizations.documents.create';
    case EditDocumentsOfOrganizations = 'organizations.documents.edit';
    case DeleteDocumentsOfOrganizations = 'organizations.documents.delete';

    // Users and abilities
    case ViewUsers = 'users.view';
    case CreateUsers = 'users.create';
    case EditUsers = 'users.edit';

    case ViewUserRoles = 'user_roles.view';
    case CreateUserRoles = 'user_roles.create';
    case EditUserRoles = 'user_roles.edit';

    case ViewAccount = 'users.view_account';
    case ViewAbilities = 'users.view_account.abilities';
    case EditAccount = 'users.edit_account';

    case ViewApiDocumentation = 'api.docs.view';
    case ManagePersonalAccessTokens = 'personal_access_tokens.manage_own';

    public function dependsOnAbility(): ?self
    {
        return match ($this) {
            self::CreateEvents,
            self::EditEvents,
            self::ViewPrivateEvents,
            self::ViewResponsibilitiesOfEvents,
            self::ManageBookingOptionsOfEvent => self::ViewEvents,

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
            self::ViewResponsibilitiesOfEventSeries => self::ViewEventSeries,

            // Basic data
            self::CreateOrganizations,
            self::EditOrganizations => self::ViewOrganizations,

            self::CreateLocations,
            self::EditLocations => self::ViewLocations,

            // Documents
            self::ViewCommentsOnDocuments,
            self::ChangeApprovalStatusOfDocuments => self::ViewDocuments,
            self::CommentOnDocuments => self::ViewCommentsOnDocuments,

            self::AddDocumentsToEvents,
            self::EditDocumentsOfEvents,
            self::DeleteDocumentsOfEvents => self::ViewDocumentsOfEvents,

            self::AddDocumentsToEventSeries,
            self::EditDocumentsOfEventSeries,
            self::DeleteDocumentsOfEventSeries => self::ViewDocumentsOfEventSeries,

            self::AddDocumentsToOrganizations,
            self::EditDocumentsOfOrganizations,
            self::DeleteDocumentsOfOrganizations => self::ViewDocumentsOfOrganizations,

            // Users and abilities
            self::CreateUsers,
            self::EditUsers => self::ViewUsers,

            self::CreateUserRoles,
            self::EditUserRoles => self::ViewUserRoles,

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
            self::ViewResponsibilitiesOfEvents => AbilityGroup::Events,
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
            self::ViewResponsibilitiesOfEventSeries => AbilityGroup::EventSeries,

            // Basic data
            self::ViewOrganizations,
            self::CreateOrganizations,
            self::EditOrganizations,
            self::ViewResponsibilitiesOfOrganizations => AbilityGroup::Organizations,
            self::ViewLocations,
            self::CreateLocations,
            self::EditLocations => AbilityGroup::Locations,

            // Documents
            self::ViewDocuments,
            self::ViewCommentsOnDocuments,
            self::CommentOnDocuments,
            self::ChangeApprovalStatusOfDocuments => AbilityGroup::Documents,
            self::ViewDocumentsOfEvents,
            self::AddDocumentsToEvents,
            self::EditDocumentsOfEvents,
            self::DeleteDocumentsOfEvents => AbilityGroup::DocumentsOfEvents,
            self::ViewDocumentsOfEventSeries,
            self::AddDocumentsToEventSeries,
            self::EditDocumentsOfEventSeries,
            self::DeleteDocumentsOfEventSeries => AbilityGroup::DocumentsOfEventSeries,
            self::ViewDocumentsOfOrganizations,
            self::AddDocumentsToOrganizations,
            self::EditDocumentsOfOrganizations,
            self::DeleteDocumentsOfOrganizations => AbilityGroup::DocumentsOfOrganizations,

            // Users and abilities
            self::ViewUsers,
            self::CreateUsers,
            self::EditUsers => AbilityGroup::Users,
            self::ViewUserRoles,
            self::CreateUserRoles,
            self::EditUserRoles => AbilityGroup::UserRoles,
            self::ViewAccount,
            self::ViewAbilities,
            self::EditAccount => AbilityGroup::OwnAccount,
            self::ViewApiDocumentation,
            self::ManagePersonalAccessTokens => AbilityGroup::ApiAccess,
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

            // Basic data
            self::ViewOrganizations => __('View organizations'),
            self::CreateOrganizations => __('Create organizations'),
            self::EditOrganizations => __('Edit organizations'),
            self::ViewResponsibilitiesOfOrganizations => __('View responsibilities of organizations'),

            self::ViewLocations => __('View locations'),
            self::CreateLocations => __('Create locations'),
            self::EditLocations => __('Edit locations'),

            // Documents
            self::ViewDocuments => __('View documents'),
            self::ViewCommentsOnDocuments => __('View comments on documents'),
            self::CommentOnDocuments => __('Comment on documents'),
            self::ChangeApprovalStatusOfDocuments => __('Change approval status of documents'),

            self::ViewDocumentsOfEvents => __('View documents of events'),
            self::AddDocumentsToEvents => __('Add documents to events'),
            self::EditDocumentsOfEvents => __('Update documents of events'),
            self::DeleteDocumentsOfEvents => __('Delete documents of events'),

            self::ViewDocumentsOfEventSeries => __('View documents of event series'),
            self::AddDocumentsToEventSeries => __('Add documents to event series'),
            self::EditDocumentsOfEventSeries => __('Update documents of event series'),
            self::DeleteDocumentsOfEventSeries => __('Delete documents of event series'),

            self::ViewDocumentsOfOrganizations => __('View documents of organizations'),
            self::AddDocumentsToOrganizations => __('Add documents to organizations'),
            self::EditDocumentsOfOrganizations => __('Update documents of organizations'),
            self::DeleteDocumentsOfOrganizations => __('Delete documents of organizations'),

            // Users and abilities
            self::ViewUsers => __('View users'),
            self::CreateUsers => __('Create users'),
            self::EditUsers => __('Edit users'),

            self::ViewUserRoles => __('View user roles'),
            self::CreateUserRoles => __('Create user roles'),
            self::EditUserRoles => __('Edit user roles'),

            self::ViewAccount => __('View own account'),
            self::ViewAbilities => __('View abilities'),
            self::EditAccount => __('Edit own account'),

            self::ManagePersonalAccessTokens => __('Manage personal access tokens'),
            self::ViewApiDocumentation => __('View API documentation'),
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
