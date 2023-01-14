@extends('layouts.app')

@php
    /** @var \App\Models\Booking $booking */
@endphp

@section('title')
    @isset($booking->booked_at)
        {{ __('Booking no. :id of :date', [
            'id' => $booking->id,
            'date' => formatDateTime($booking->booked_at)
        ]) }}
    @else
        {{ __('Booking no. :id', [
            'id' => $booking->id,
        ]) }}
    @endisset
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('events.index') }}">{{ __('Events') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('events.show', $booking->bookingOption->event) }}">{{ $booking->bookingOption->event->name }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('booking-options.show', [$booking->bookingOption->event, $booking->bookingOption]) }}">{{ $booking->bookingOption->name }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('bookings.index', [$booking->bookingOption->event, $booking->bookingOption]) }}">{{ __('Bookings') }}</x-nav.breadcrumb>
@endsection

@section('content')
    <x-form method="PUT" action="{{ route('bookings.update', $booking) }}">
        @include('bookings.booking_form_fields', [
            'booking' => $booking,
            'bookingOption' => $booking->bookingOption,
        ])

        <x-button.group>
            <x-button.save>
                @isset($booking)
                    {{ __( 'Save' ) }}
                @else
                    {{ __('Create') }}
                @endisset
            </x-button.save>
            <x-button.cancel href="{{ route('bookings.index', [$booking->bookingOption->event, $booking->bookingOption]) }}"/>
        </x-button.group>
    </x-form>

    <x-text.timestamp :model="$booking ?? null"/>
@endsection
