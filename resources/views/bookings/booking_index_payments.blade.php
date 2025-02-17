@extends('layouts.app')

@php
    /** @var \App\Models\Event $event */
    /** @var \App\Models\BookingOption $bookingOption */

    $unpaidBookings = $bookingOption->bookings->whereNull('paid_at');
    $paidBookings = $bookingOption->bookings->whereNotNull('paid_at')->sortBy('paid_at');
@endphp

@section('title')
    {{ __('Payments') }} | {{ $bookingOption->event->name }}: {{ $bookingOption->name }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('events.show', $event) }}">{{ $event->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ $bookingOption->name }}</x-bs::breadcrumb.item>
    @can('viewBookings', \App\Models\BookingOption::class)
        <x-bs::breadcrumb.item href="{{ route('bookings.index', [$event, $bookingOption]) }}">{{ __('Bookings') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('Bookings') }}</x-bs::breadcrumb.item>
    @endcan
    <x-bs::breadcrumb.item>{{ __('Payments') }}</x-bs::breadcrumb.item>
@endsection

@section('headline')
    <h1>{{ $bookingOption->event->name }}: {{ $bookingOption->name }}</h1>
@endsection

@section('headline-buttons')
    @can('book', $bookingOption)
        <x-button.create href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ __('Book') }}</x-button.create>
    @endcan
    @can('viewGroups', $event)
        <x-bs::button.link href="{{ route('groups.index', $event) }}" variant="secondary">
            <i class="fa fa-fw fa-people-group"></i> {{ __('Groups') }} <x-bs::badge variant="danger">{{ formatInt($event->groups->count()) }}</x-bs::badge>
        </x-bs::button.link>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-6 col-lg-3">
            <h2>{{ __('Not yet paid') }}</h2>
            <div class="mb-3">
                <x-bs::badge :variant="$unpaidBookings->isEmpty() ? 'success' : 'danger'">{{ formatTransChoice(':count bookings', $unpaidBookings->count()) }}</x-bs::badge>
            </div>
            @if($unpaidBookings->isNotEmpty())
                <x-bs::list>
                    @foreach($unpaidBookings->sortBy(fn (\App\Models\Booking $booking) => $booking->booked_at) as $booking)
                        @php
                            $group = $booking->getGroup($event);
                        @endphp
                        <x-bs::list.item>
                            <div>
                                @can('view', $booking)
                                    <a href="{{ route('bookings.show', $booking) }}" target="_blank">{{ $booking->first_name }} <strong>{{ $booking->last_name }}</strong></a>
                                @else
                                    {{ $booking->first_name }} <strong>{{ $booking->last_name }}</strong>
                                @endcan
                                @isset($group)
                                    ({{ $group->name }})
                                @endisset
                                <div class="small text-nowrap">
                                    <i class="fa fa-fw fa-clock" title="{{ __('Booking date') }}"></i>
                                    @isset($booking->booked_at)
                                        {{ formatDateTime($booking->booked_at) }}
                                    @else
                                        <x-bs::badge variant="danger">{{ __('Booking not completed yet') }}</x-bs::badge>
                                    @endisset
                                </div>
                            </div>
                            <x-slot:end>
                                <x-bs::badge :variant="$booking->price === $bookingOption->price ? 'primary' : 'secondary'">{{ formatDecimal($booking->price) }}&nbsp;€</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                    @endforeach
                </x-bs::list>
            @endif
        </div>
        <div class="col-12 col-md-6 col-lg-9">
            <h2>{{ __('Already paid') }}</h2>
            <div class="mb-3">
                <x-bs::badge>{{ formatTransChoice(':count bookings', $paidBookings->count()) }}</x-bs::badge>
                @if($paidBookings->isNotEmpty())
                    <x-bs::badge>{{ __('Latest payment') }}: {{ formatDate($paidBookings->last()->paid_at) }}</x-bs::badge>
                @endif
            </div>
            @if($paidBookings->isNotEmpty())
                <div class="cols-lg-2 cols-xxl-3">
                    @foreach($paidBookings->groupBy(fn ($booking) => formatDate($booking->paid_at)) as $date => $bookings)
                        <div class="avoid-break mb-3 pe-2">
                            <x-bs::list>
                                <x-bs::list.item variant="primary">{{ __('Paid at') }} {{ $date }}</x-bs::list.item>
                                @foreach($bookings as $booking)
                                    @php
                                        $group = $booking->getGroup($event);
                                    @endphp
                                    <x-bs::list.item>
                                        <div>
                                            @can('view', $booking)
                                                <a href="{{ route('bookings.show', $booking) }}" target="_blank">{{ $booking->first_name }} <strong>{{ $booking->last_name }}</strong></a>
                                            @else
                                                {{ $booking->first_name }} <strong>{{ $booking->last_name }}</strong>
                                            @endcan
                                            @isset($group)
                                                ({{ $group->name }})
                                            @endisset
                                            <div class="small text-nowrap">
                                                <i class="fa fa-fw fa-clock" title="{{ __('Booking date') }}"></i>
                                                @isset($booking->booked_at)
                                                    {{ formatDateTime($booking->booked_at) }}
                                                @else
                                                    <x-bs::badge variant="danger">{{ __('Booking not completed yet') }}</x-bs::badge>
                                                @endisset
                                            </div>
                                        </div>
                                        <x-slot:end>
                                            <x-bs::badge :variant="$booking->price === $bookingOption->price ? 'primary' : 'secondary'">{{ formatDecimal($booking->price) }}&nbsp;€</x-bs::badge>
                                        </x-slot:end>
                                    </x-bs::list.item>
                                @endforeach
                            </x-bs::list>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endsection
