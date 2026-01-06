@extends('layouts.app')

@php
    /** @var \App\Models\Event $event */
    /** @var \App\Models\BookingOption $bookingOption */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Booking[] $bookings */

    $hasGroups = $event->groups->isNotEmpty();
@endphp

@section('title')
    {{ __('Bookings') }} | {{ $bookingOption->event->name }}: {{ $bookingOption->name }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('events.show', $event) }}">{{ $event->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ $bookingOption->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item>{{ __('Bookings') }}</x-bs::breadcrumb.item>
@endsection

@section('headline')
    <h1>{{ $bookingOption->event->name }}: {{ $bookingOption->name }}</h1>
@endsection

@section('headline-buttons')
    @can('book', $bookingOption)
        <x-bs::button.link href="{{ route('booking-options.show', [$event, $bookingOption]) }}">
            <i class="fa fa-fw fa-plus"></i> {{ __('Book') }}
        </x-bs::button.link>
    @endcan
    @can('viewAnyPaymentStatus', \App\Models\Booking::class)
        <x-bs::button.link href="{{ route('bookings.index.payments', [$event, $bookingOption]) }}">
            <i class="fa fa-fw fa-euro-sign"></i> {{ __('Payments') }}
        </x-bs::button.link>
    @endcan
    @can('viewGroups', $event)
        <x-bs::button.link href="{{ route('groups.index', $event) }}" variant="secondary">
            <i class="fa fa-fw fa-people-group"></i> {{ __('Groups') }} <x-bs::badge variant="danger">{{ formatInt($event->groups->count()) }}</x-bs::badge>
        </x-bs::button.link>
    @endcan
@endsection

@section('content')
    <x-form.filter>
        <div class="row">
            <div class="col-12 col-lg-3">
                <x-bs::form.field id="name" name="filter[name]" type="text"
                                  :from-query="true">{{ __('Name') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-3">
                <x-bs::form.field id="postal_code" name="filter[postal_code]" type="text"
                                  :from-query="true">{{ __('Postal code') }}</x-bs::form.field>
            </div>
            @can('viewAnyPaymentStatus', \App\Models\Booking::class)
                <div class="col-12 col-lg-3">
                    <x-bs::form.field id="payment_status" name="filter[payment_status]" type="select"
                                      :options="\App\Enums\PaymentStatus::toOptionsWithAll()"
                                      :cast="\App\Enums\FilterValue::castToIntIfNoValue()"
                                      :from-query="true"><i class="fa fa-fw fa-euro"></i> {{ __('Payment status') }}</x-bs::form.field>
                </div>
            @endcan
            @if($hasGroups)
                <div class="col-12 col-lg-3">
                    <x-bs::form.field id="group_id" name="filter[group_id]" type="select"
                                      :options="\Portavice\Bladestrap\Support\Options::fromModels($event->groups, 'name')->prepend(__('all'), \App\Enums\FilterValue::All->value)"
                                      :from-query="true"><i class="fa fa-fw fa-people-group"></i> {{ __('Group') }}</x-bs::form.field>
                </div>
            @endif
            <div class="col-12 col-lg-3">
                <x-bs::form.field id="trashed" name="filter[trashed]" type="select"
                                  :options="\App\Enums\DeletedFilter::toOptions()"
                                  :from-query="true"><i class="fa fa-fw fa-trash"></i> {{ __('Show trashed?') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\Booking::sortOptions()->getNamesWithLabels()"
                                  :from-query="true"><i class="fa fa-fw fa-sort"></i> {{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>

        <x-slot:addButtons>
            <x-bs::button.group>
                <x-bs::dropdown.button :nested-in-group="true">
                    <i class="fa fa-fw fa-download"></i> {{ __('Export') }}
                    <x-slot:dropdown>
                        @can('exportBookings', $bookingOption)
                            <li><button class="dropdown-item" type="submit" name="output" value="export"><i class="fa fa-fw fa-file-excel"></i> {{ __('Excel file') }}</button></li>
                        @endcan
                        <li><button class="dropdown-item" type="submit" name="output" value="pdf"><i class="fa fa-fw fa-file-pdf"></i> {{ __('PDFs in zip file') }}</button></li>
                        @foreach($bookingOption->formFieldsForFiles as $formField)
                            <li><button class="dropdown-item" type="submit" name="output" value="{{ $formField->id }}"><i class="fa fa-fw fa-file"></i> {{ $formField->name }}</button></li>
                        @endforeach
                    </x-slot:dropdown>
                </x-bs::dropdown.button>
            </x-bs::button.group>
        </x-slot:addButtons>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$bookings->total()"/>

    <div class="row my-3">
        @foreach($bookings as $booking)
            @php
                $booking->setRelation('bookingOption', $bookingOption);
            @endphp
            <div class="col-12 col-md-6 col-lg-4 col-xxl-3 mb-3">
                <div class="card avoid-break">
                    <div @class([
                        'card-header',
                        'text-bg-danger' => $booking->trashed(),
                    ])>
                        <h2 class="card-title">
                            @can('view', $booking)
                                <a href="{{ route('bookings.show', $booking) }}">{{ $booking->first_name }} {{ $booking->last_name }}</a>
                            @else
                                {{ $booking->first_name }} <strong>{{ $booking->last_name }}</strong>
                            @endcan
                        </h2>
                        <div class="card-subtitle">
                            <x-bs::badge variant="light"><i class="fa fw-fw fa-hashtag"></i> {{ $booking->id }}</x-bs::badge>
                            <x-badge.enum :case="$booking->status"/>
                        </div>
                    </div>
                    <x-bs::list :flush="true">
                        @if($hasGroups)
                            @php
                                $group = $booking->getGroup($event);
                            @endphp
                            <x-bs::list.item>
                                <i class="fa fa-fw fa-people-group" title="{{ __('Group') }}"></i>
                                @isset($group)
                                    <strong>{{ $group->name }}</strong>
                                @else
                                    <strong class="text-danger">{{ __('none') }}</strong>
                                @endisset
                            </x-bs::list.item>
                        @endif
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-clock" title="{{ __('Booking date') }}"></i>
                            @isset($booking->booked_at)
                                {{ formatDateTime($booking->booked_at) }}
                            @else
                                <x-bs::badge variant="danger">{{ __('Booking not completed yet') }}</x-bs::badge>
                            @endisset
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-user" title="{{ __('Booked by') }}"></i>
                            @isset($booking->bookedByUser)
                                <span title="{{ $booking->bookedByUser->email }}">
                                    @can('view', $booking->bookedByUser)
                                        <a href="{{ route('users.show', $booking->bookedByUser) }}">{{ $booking->bookedByUser->name }}</a>
                                    @else
                                        {{ $booking->bookedByUser->name }}
                                    @endcan
                                </span>
                                @isset($booking->bookedByUser->email_verified_at)
                                    <x-bs::badge variant="success">{{ __('verified') }}</x-bs::badge>
                                @else
                                    <x-bs::badge variant="danger">{{ __('not verified') }}</x-bs::badge>
                                @endisset
                            @else
                                {{ __('Guest') }}
                            @endisset
                        </x-bs::list.item>
                        @can('updateBookingComment', $booking)
                            <x-bs::list.item>
                                <i class="fa fa-fw fa-comment" title="{{ __('Comment') }}"></i>
                                <span>{{ $booking->comment ?? '—' }}</span>
                            </x-bs::list.item>
                        @endcan
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-euro" title="{{ __('Price') }}"></i>
                            @include('bookings.shared.payment-status')
                        </x-bs::list.item>
                        @isset($booking->date_of_birth)
                            <x-bs::list.item>
                                <span class="text-nowrap"><i class="fa fa-fw fa-cake-candles" title="{{ __('Date of birth') }}"></i></span>
                                <span>
                                    <span class="me-2">{{ formatDate($booking->date_of_birth) }}</span>
                                    @isset($booking->age)
                                        <x-bs::badge>{{ formatTransChoiceDecimal(':count years', $booking->age, 1) }}</x-bs::badge>
                                    @endisset
                                </span>
                            </x-bs::list.item>
                        @endisset
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-at"></i>
                            <a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-phone"></i>
                            @isset($booking->phone)
                                <a href="{{ $booking->phone_link }}">{{ $booking->phone }}</a>
                            @else
                                {{ __('none') }}
                            @endisset
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-road"></i>
                            <span class="d-inline-block">
                                <div class="d-flex flex-column">
                                    @if($booking->hasAnyFilledAddressField())
                                        <div>{{ $booking->streetLine }}</div>
                                        <div>{{ $booking->cityLine }}</div>
                                        <div>{{ $booking->country }}</div>
                                    @else
                                        {{ __('none') }}
                                    @endif
                                </div>
                            </span>
                        </x-bs::list.item>
                    </x-bs::list>
                    @canany(['viewPDF', 'update', 'delete', 'restore'], $booking)
                        <div class="card-body d-flex flex-wrap gap-1 d-print-none">
                            @can('viewPDF', $booking)
                                <x-bs::button.link variant="secondary" href="{{ route('bookings.show-pdf', $booking) }}" class="text-nowrap">
                                    <i class="fa fa-file-pdf"></i> {{ __('PDF') }}
                                </x-bs::button.link>
                            @endcan
                            @can('update', $booking)
                                <x-button.edit href="{{ route('bookings.edit', $booking) }}" class="text-nowrap"/>
                            @endcan
                            @can('delete', $booking)
                                <x-bs::button variant="danger" form="delete-{{ $booking->id }}">
                                    <i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}
                                </x-bs::button>
                            @elsecan('restore', $booking)
                                <x-bs::button variant="success" form="restore-{{ $booking->id }}">
                                    <i class="fa fa-fw fa-trash-can-arrow-up"></i> {{ __('Restore') }}
                                </x-bs::button>
                            @endcan
                            @can('delete', $booking)
                                <x-bs::form id="delete-{{ $booking->id }}" method="DELETE"
                                            action="{{ route('bookings.delete', $booking) }}"/>
                            @elsecan('restore', $booking)
                                <x-bs::form id="restore-{{ $booking->id }}" method="PATCH"
                                            action="{{ route('bookings.restore', $booking) }}"/>
                            @endcan
                        </div>
                    @endcan
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$booking"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $bookings->withQueryString()->links() }}
@endsection
