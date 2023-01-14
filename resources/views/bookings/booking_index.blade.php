@extends('layouts.app')

@php
    /** @var \App\Models\Event $event */
    /** @var \App\Models\BookingOption $bookingOption */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Booking[] $bookings */
@endphp

@section('title')
    {{ $bookingOption->event->name }}: {{ $bookingOption->name }} | {{ __('Bookings') }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('events.index') }}">{{ __('Events') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('events.show', $event) }}">{{ $event->name }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ $bookingOption->name }}</x-nav.breadcrumb>
    <x-nav.breadcrumb>{{ __('Bookings') }}</x-nav.breadcrumb>
@endsection

@section('headline')
    <h1>{{ $bookingOption->event->name }}: {{ $bookingOption->name }}</h1>
@endsection

@section('headline-buttons')
    @can('create', $bookingOption)
        <x-button.create href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ __('Book') }}</x-button.create>
    @endcan
@endsection

@section('content')
    <x-form.filter method="GET">
        <x-form.row>
            <x-form.label for="search">{{ __('Search term') }}</x-form.label>
            <x-form.input id="search" name="filter[search]"/>
        </x-form.row>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$bookings->total()"/>

    <div class="row my-3">
        @foreach($bookings as $booking)
            <div class="col-12 col-md-6 col-lg-4 col-xxl-3 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ $booking->first_name }} {{ $booking->last_name }}</h2>
                        <div class="card-subtitle text-muted">
                            @isset($booking->booked_at)
                                {{ formatDateTime($booking->booked_at) }}
                            @else
                                <span class="badge bg-primary">{{ __('Booking not completed yet') }}</span>
                            @endisset
                        </div>
                    </div>
                    <x-list.group class="list-group-flush">
                        <x-list.item>
                            {{ $booking->email }}
                            <br/>{{ $booking->phone }}
                        </x-list.item>
                        <x-list.item>
                            {{ $booking->streetLine }}
                            <br/>{{ $booking->cityLine }}
                            <br/>{{ $booking->country }}
                        </x-list.item>
                    </x-list.group>
                    <div class="card-body">
                        <x-button.edit href="{{ route('bookings.edit', $booking) }}"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $bookings->withQueryString()->links() }}
@endsection
