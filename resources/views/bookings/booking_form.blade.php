@extends('layouts.app')

@php
    /** @var \App\Models\Booking $booking */
    $bookingOption = $booking->bookingOption;
    $event = $bookingOption->event;
@endphp

@section('title')
    {{ __('Edit booking no. :id', [
        'id' => $booking->id,
    ]) }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('events.index') }}">{{ __('Events') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('events.show', $event) }}">{{ $event->name }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ $bookingOption->name }}</x-nav.breadcrumb>
    @can('viewAny', \App\Models\Booking::class)
        <x-nav.breadcrumb href="{{ route('bookings.index', [$event, $bookingOption]) }}">{{ __('Bookings') }}</x-nav.breadcrumb>
    @endcan
@endsection

@section('headline')
    <h1>{{ $event->name }}: {{ $bookingOption->name }}</h1>
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-4">
            @include('events.shared.event_details')
        </div>
        <div class="col-12 col-md-8">
            @include('bookings.shared.booking_details')

            <x-form method="PUT" action="{{ route('bookings.update', $booking) }}">
                @include('bookings.booking_form_fields', [
                    'booking' => $booking,
                    'bookingOption' => $bookingOption,
                ])

                <x-button.group>
                    <x-button.save>
                        @isset($booking)
                            {{ __( 'Save' ) }}
                        @else
                            {{ __('Create') }}
                        @endisset
                    </x-button.save>
                    <x-button.cancel href="{{ route('bookings.index', [$event, $bookingOption]) }}"/>
                </x-button.group>
            </x-form>
        </div>
    </div>

    <x-text.timestamp :model="$booking ?? null"/>
@endsection
