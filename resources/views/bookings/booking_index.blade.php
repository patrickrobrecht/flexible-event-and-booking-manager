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
                <x-bs::form.field id="phone" name="filter[phone]" type="text"
                                  :from-query="true"><i class="fa fa-fw fa-phone"></i> {{ __('Phone number') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-3">
                <x-bs::form.field id="email" name="filter[email]" type="text"
                                  :from-query="true"><i class="fa fa-fw fa-at"></i> {{ __('E-mail') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-3">
                <x-bs::form.field id="postal_code" name="filter[postal_code]" type="text"
                                  :from-query="true">{{ __('Postal code') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-3">
                <x-bs::form.field id="status" name="filter[status]" type="select"
                                  :options="\App\Enums\BookingStatus::toOptionsWithAll()"
                                  :cast="\App\Enums\FilterValue::castToIntIfNoValue()"
                                  :from-query="true">{{ __('Status of the booking') }}</x-bs::form.field>
            </div>
            @can('viewAnyPaymentStatus', \App\Models\Booking::class)
                <div class="col-12 col-lg-3">
                    <x-bs::form.field id="payment_status" name="filter[payment_status]" type="select"
                                      :options="\App\Enums\PaymentStatus::toOptionsWithAll()"
                                      :cast="\App\Enums\FilterValue::castToIntIfNoValue()"
                                      :from-query="true"><i class="fa fa-fw fa-euro"></i> {{ __('Payment status') }}</x-bs::form.field>
                </div>
            @endcan
            <div class="col-12 col-lg-3">
                <x-bs::form.field id="trashed" name="filter[trashed]" type="select"
                                  :options="\App\Enums\DeletedFilter::toOptions()"
                                  :from-query="true"><i class="fa fa-fw fa-trash"></i> {{ __('Show trashed?') }}</x-bs::form.field>
            </div>
            @if($hasGroups)
                <div class="col-12 col-lg-3">
                    <x-bs::form.field id="group_id" name="filter[group_id]" type="select"
                                      :options="\Portavice\Bladestrap\Support\Options::fromModels($event->groups, 'name')->prepend(__('all'), \App\Enums\FilterValue::All->value)"
                                      :from-query="true"><i class="fa fa-fw fa-people-group"></i> {{ __('Group') }}</x-bs::form.field>
                </div>
            @endif
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
                @include('bookings.shared.booking_card', [
                    'showEvent' => false,
                    'showGroups' => $hasGroups,
                ])
            </div>
        @endforeach
    </div>

    {{ $bookings->withQueryString()->links() }}
@endsection
