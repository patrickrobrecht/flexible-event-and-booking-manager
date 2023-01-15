@extends('layouts.app')

@php
    /** @var \App\Models\BookingOption $bookingOption */
@endphp

@section('title')
    {{ $bookingOption->event->name }}: {{ $bookingOption->name }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('events.index') }}">{{ __('Events') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('events.show', $bookingOption->event) }}">{{ $bookingOption->event->name }}</x-nav.breadcrumb>
    <x-nav.breadcrumb>{{ $bookingOption->name }}</x-nav.breadcrumb>
@endsection

@section('headline-buttons')
    @can('update', $bookingOption)
        <x-button.edit href="{{ route('booking-options.edit', [$event, $bookingOption]) }}"/>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-4">
            @include('events.shared.event_details')
        </div>
        <div class="col-12 col-md-8">
            @if(!isset($bookingOption->available_from) || $bookingOption->available_from->isFuture())
                <p class="alert alert-danger">
                    {{ __('Bookings are not possible yet.') }}
                </p>
            @elseif(isset($bookingOption->available_until) && $bookingOption->available_until->isPast())
                <p class="alert alert-danger">
                    {{ __('The booking period ended at :date.', ['date' => formatDateTime($bookingOption->available_until)]) }}
                    {{ __('Bookings are not possible anymore.') }}
                </p>
            @elseif($bookingOption->hasReachedMaximumBookings())
                <p class="alert alert-danger">
                    {{ __('The maximum number of bookings has been reached.') }}
                    {{ __('Bookings are not possible anymore.') }}
                </p>
            @else
                @can('book', $bookingOption)
                    <x-form method="POST" action="{{ route('bookings.store', [$event, $bookingOption]) }}">
                        @include('bookings.booking_form_fields', [
                            'booking' => null,
                            'bookingOption' => $bookingOption,
                        ])

                        <x-button.save>
                            @isset($bookingOption->price)
                                {{ __('Book with costs') }}
                                ({{ formatDecimal($bookingOption->price) }}&nbsp;€)
                            @else
                                {{ __('Book') }}
                            @endisset
                        </x-button.save>
                    </x-form>
                @endcan
            @endif
        </div>
    </div>
@endsection
