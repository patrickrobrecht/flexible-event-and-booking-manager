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
        @can('viewAnyPaymentStatus', \App\Models\Booking::class)
            <div class="row">
                <div class="col-12 col-md-9">
                    <x-form.row>
                        <x-form.label for="search">{{ __('Search term') }}</x-form.label>
                        <x-form.input id="search" name="filter[search]"/>
                    </x-form.row>
                </div>
                <div class="col-12 col-md-3">
                    <x-form.row>
                        <x-form.label for="payment_status">{{ __('Payment status') }}</x-form.label>
                        <x-form.select id="payment_status" name="filter[payment_status]"
                                       :options="\App\Options\PaymentStatus::keysWithNamesAndAll()"/>
                    </x-form.row>
                </div>
            </div>
        @else
            <x-form.row>
                <x-form.label for="search">{{ __('Search term') }}</x-form.label>
                <x-form.input id="search" name="filter[search]"/>
            </x-form.row>
        @endcan
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$bookings->total()"/>

    <div class="row my-3">
        @foreach($bookings as $booking)
            <div class="col-12 col-md-6 col-lg-4 col-xxl-3 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ $booking->first_name }} {{ $booking->last_name }}</h2>
                        <div class="card-subtitle text-muted">{{ $bookingOption->name }}</div>
                    </div>
                    <x-list.group class="list-group-flush">
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-clock"></i>
                                @isset($booking->booked_at)
                                    {{ formatDateTime($booking->booked_at) }}
                                @else
                                    <span class="badge bg-primary">{{ __('Booking not completed yet') }}</span>
                                @endisset
                            </span>
                        </x-list.item>
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-user"></i>
                                @isset($booking->bookedByUser)
                                    {{ $booking->bookedByUser->first_name }} {{ $booking->bookedByUser->last_name }}
                                @else
                                    {{ __('Guest') }}
                                @endisset
                            </span>
                        </x-list.item>
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-euro"></i>
                                @isset($booking->price)
                                    {{ formatDecimal($booking->price) }}&nbsp;€
                                    @can('viewPaymentStatus', $booking)
                                        @isset($booking->paid_at)
                                            <span class="badge bg-primary">{{ __('paid') }} ({{ $booking->paid_at->isMidnight()
                                            ? formatDate($booking->paid_at)
                                            : formatDateTime($booking->paid_at) }})</span>
                                        @else
                                            <span class="badge bg-danger">{{ __('not paid yet') }}</span>
                                        @endisset
                                    @endcan
                                @else
                                    <span class="badge bg-primary">{{ __('free of charge') }}</span>
                                @endisset
                            </span>
                        </x-list.item>
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
                        @can('view', $booking)
                            <x-button.secondary href="{{ route('bookings.show', $booking) }}">
                                <i class="fa fa-eye"></i>
                                {{ __('View') }}
                            </x-button.secondary>
                        @endcan
                        @can('update', $booking)
                            <x-button.edit href="{{ route('bookings.edit', $booking) }}"/>
                        @endcan
                    </div>
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$booking"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $bookings->withQueryString()->links() }}
@endsection
