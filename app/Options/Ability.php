<?php

namespace App\Options;

use App\Options\Traits\NamedOption;

enum Ability: string
{
    use NamedOption;

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
    case EditBookingComment = 'events.edit_booking_comment';
    case ViewPaymentStatus = 'events.view_payment_status';
    case EditPaymentStatus = 'events.edit_payment_status';

    case ViewEventSeries = 'event_series.view';
    case ViewPrivateEventSeries = 'event_series.view_private';
    case CreateEventSeries = 'event_series.create';
    case EditEventSeries = 'event_series.edit';

    case ViewForms = 'forms.view';
    case CreateForms = 'forms.create';
    case EditForms = 'forms.edit';

    case ViewLocations = 'locations.view';
    case CreateLocations = 'locations.create';
    case EditLocations = 'locations.edit';

    case ViewOrganizations = 'organizations.view';
    case CreateOrganizations = 'organizations.create';
    case EditOrganizations = 'organizations.edit';

    case ViewUsers = 'users.view';
    case CreateUsers = 'users.create';
    case EditUsers = 'users.edit';

    case ViewUserRoles = 'user_roles.view';
    case CreateUserRoles = 'user_roles.create';
    case EditUserRoles = 'user_roles.edit';

    public function getTranslatedName(): string
    {
        return match($this) {
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
            self::EditBookingComment => __('Edit booking comment'),
            self::ViewPaymentStatus => __('View payment status'),
            self::EditPaymentStatus => __('Edit payment status'),

            self::ViewEventSeries => __('View event series'),
            self::ViewPrivateEventSeries => __('View private event series'),
            self::CreateEventSeries => __('Create event series'),
            self::EditEventSeries => __('Edit event series'),

            self::ViewForms => __('View forms'),
            self::CreateForms => __('Create forms'),
            self::EditForms => __('Edit forms'),

            self::ViewLocations => __('View locations'),
            self::CreateLocations => __('Create locations'),
            self::EditLocations => __('Edit locations'),

            self::ViewOrganizations => __('View organizations'),
            self::CreateOrganizations => __('Create organizations'),
            self::EditOrganizations => __('Edit organizations'),

            self::ViewUsers => __('View users'),
            self::CreateUsers => __('Create users'),
            self::EditUsers => __('Edit users'),

            self::ViewUserRoles => __('View user roles'),
            self::CreateUserRoles => __('Create user roles'),
            self::EditUserRoles => __('Edit user roles'),
        };
    }
}
