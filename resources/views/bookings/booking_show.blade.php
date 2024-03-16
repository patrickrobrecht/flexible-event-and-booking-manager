@extends('layouts.app')

@php
    /** @var \App\Models\Booking $booking */
    $bookingOption = $booking->bookingOption;
    $event = $bookingOption->event;
@endphp

@section('title')
    {{ __('Booking no. :id', [
        'id' => $booking->id,
    ]) }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('events.show', $event) }}">{{ $event->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ $bookingOption->name }}</x-bs::breadcrumb.item>
    @can('viewBookings', $bookingOption)
        <x-bs::breadcrumb.item href="{{ route('bookings.index', [$event, $bookingOption]) }}">{{ __('Bookings') }}</x-bs::breadcrumb.item>
    @endcan
@endsection

@section('headline')
    <h1>{{ $event->name }}: {{ $bookingOption->name }}</h1>
@endsection

@section('headline-buttons')
    @can('update', $booking)
        <x-button.edit href="{{ route('bookings.edit', $booking) }}"/>
    @endcan
    @include('bookings.shared.booking_buttons')
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-4">
            @include('events.shared.event_details')
        </div>
        <div class="col-12 col-md-8">
            @include('bookings.shared.booking_details')

            @include('bookings.booking_form_fields', [
                'booking' => $booking,
                'bookingOption' => $bookingOption,
                'canEdit' => false,
            ])
        </div>
    </div>

    <x-text.timestamp :model="$booking ?? null"/>
@endsection
