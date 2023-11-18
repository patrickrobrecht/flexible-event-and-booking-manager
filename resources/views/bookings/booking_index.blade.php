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
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('events.show', $event) }}">{{ $event->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ $bookingOption->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item>{{ __('Bookings') }}</x-bs::breadcrumb.item>
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

        <x-slot:addButtons>
            @can('exportAny', \App\Models\Booking::class)
                <button type="submit" class="btn btn-primary" name="output" value="export">
                    <i class="fa fa-download"></i>
                    {{ __('Export') }}
                </button>
            @endcan
        </x-slot:addButtons>
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
                    <x-bs::list :flush="true">
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
                                <span title="{{ $booking->bookedByUser->email }}">{{ $booking->bookedByUser->first_name }} {{ $booking->bookedByUser->last_name }}</span>
                            @else
                                {{ __('Guest') }}
                            @endisset
                            @isset($booking->bookedByUser)
                                @isset($booking->bookedByUser->email_verified_at)
                                    <x-bs::badge variant="success">{{ __('verified') }}</x-bs::badge>
                                @else
                                    <x-bs::badge variant="danger">{{ __('not verified') }}</x-bs::badge>
                                @endisset
                            @endisset
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-euro" title="{{ __('Price') }}"></i>
                            @isset($booking->price)
                                {{ formatDecimal($booking->price) }}&nbsp;€
                                @can('viewPaymentStatus', $booking)
                                    @isset($booking->paid_at)
                                        <x-bs::badge variant="success">{{ __('paid') }} ({{ $booking->paid_at->isMidnight()
                                            ? formatDate($booking->paid_at)
                                            : formatDateTime($booking->paid_at) }})</x-bs::badge>
                                    @else
                                        <x-bs::badge variant="danger">{{ __('not paid yet') }}</x-bs::badge>
                                    @endisset
                                @endcan
                            @else
                                <x-bs::badge variant="primary">{{ __('free of charge') }}</x-bs::badge>
                            @endisset
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-at"></i>
                            {{ $booking->email }}
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-phone"></i>
                            {{ $booking->phone ?? __('none') }}
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
                    <div class="card-body">
                        @can('view', $booking)
                            <x-bs::button.link variant="secondary" href="{{ route('bookings.show', $booking) }}">
                                <i class="fa fa-eye"></i>
                                {{ __('View') }}
                            </x-bs::button.link>
                        @endcan
                        @can('viewPDF', $booking)
                            <x-bs::button.link variant="secondary" href="{{ route('bookings.show-pdf', $booking) }}">
                                <i class="fa fa-file-pdf"></i>
                                {{ __('PDF') }}
                            </x-bs::button.link>
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
