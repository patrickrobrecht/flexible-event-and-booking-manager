<?php

namespace App\Options;

use App\Options\Traits\NamedOption;

enum Ability: string
{
    use NamedOption;

    case ViewAccount = 'users.view_account';
    case EditAccount = 'users.edit_account';
    case ManagePersonalAccessTokens = 'personal_access_tokens.manage_own';

    case ViewEvents = 'events.view';
    case ViewPrivateEvents = 'events.view_private';
    case CreateEvents = 'events.create';
    case EditEvents = 'events.edit';
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
    case ViewDocumentsOfEvents = 'events.documents.view';
    case AddDocumentsToEvents = 'events.documents.create';
    case EditDocumentsOfEvents = 'events.documents.edit';
    case DeleteDocumentsOfEvents = 'events.documents.delete';

    case ViewEventSeries = 'event_series.view';
    case ViewPrivateEventSeries = 'event_series.view_private';
    case CreateEventSeries = 'event_series.create';
    case EditEventSeries = 'event_series.edit';
    case ViewDocumentsOfEventSeries = 'event_series.documents.view';
    case AddDocumentsToEventSeries = 'event_series.documents.create';
    case EditDocumentsOfEventSeries = 'event_series.documents.edit';
    case DeleteDocumentsOfEventSeries = 'event_series.documents.delete';

    case ViewLocations = 'locations.view';
    case CreateLocations = 'locations.create';
    case EditLocations = 'locations.edit';

    case ViewOrganizations = 'organizations.view';
    case CreateOrganizations = 'organizations.create';
    case EditOrganizations = 'organizations.edit';
    case ViewDocumentsOfOrganizations = 'organizations.documents.view';
    case AddDocumentsToOrganizations = 'organizations.documents.create';
    case EditDocumentsOfOrganizations = 'organizations.documents.edit';
    case DeleteDocumentsOfOrganizations = 'organizations.documents.delete';

    case ViewDocuments = 'documents.view';

    case ViewUsers = 'users.view';
    case CreateUsers = 'users.create';
    case EditUsers = 'users.edit';

    case ViewUserRoles = 'user_roles.view';
    case CreateUserRoles = 'user_roles.create';
    case EditUserRoles = 'user_roles.edit';

    public function getTranslatedName(): string
    {
        return match ($this) {
            self::ViewAccount => __('View own account'),
            self::EditAccount => __('Edit own account'),
            self::ManagePersonalAccessTokens => __('Manage personal access tokens'),

            self::ViewEvents => __('View events'),
            self::ViewPrivateEvents => __('View private events'),
            self::CreateEvents => __('Create events'),
            self::EditEvents => __('Edit events'),
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
            self::ViewDocumentsOfEvents => __('View documents of events'),
            self::AddDocumentsToEvents => __('Add documents to events'),
            self::EditDocumentsOfEvents => __('Update documents of events'),
            self::DeleteDocumentsOfEvents => __('Delete documents of events'),

            self::ViewEventSeries => __('View event series'),
            self::ViewPrivateEventSeries => __('View private event series'),
            self::CreateEventSeries => __('Create event series'),
            self::EditEventSeries => __('Edit event series'),
            self::ViewDocumentsOfEventSeries => __('View documents of event series'),
            self::AddDocumentsToEventSeries => __('Add documents to event series'),
            self::EditDocumentsOfEventSeries => __('Update documents of event series'),
            self::DeleteDocumentsOfEventSeries => __('Delete documents of event series'),

            self::ViewLocations => __('View locations'),
            self::CreateLocations => __('Create locations'),
            self::EditLocations => __('Edit locations'),

            self::ViewOrganizations => __('View organizations'),
            self::CreateOrganizations => __('Create organizations'),
            self::EditOrganizations => __('Edit organizations'),
            self::ViewDocumentsOfOrganizations => __('View documents of organizations'),
            self::AddDocumentsToOrganizations => __('Add documents to organizations'),
            self::EditDocumentsOfOrganizations => __('Update documents of organizations'),
            self::DeleteDocumentsOfOrganizations => __('Delete documents of organizations'),

            self::ViewDocuments => __('View documents'),

            self::ViewUsers => __('View users'),
            self::CreateUsers => __('Create users'),
            self::EditUsers => __('Edit users'),

            self::ViewUserRoles => __('View user roles'),
            self::CreateUserRoles => __('Create user roles'),
            self::EditUserRoles => __('Edit user roles'),
        };
    }
}
