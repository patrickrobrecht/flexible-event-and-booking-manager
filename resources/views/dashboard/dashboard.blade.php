@extends('layouts.app')

@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Booking[] $bookings */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events */
@endphp

@section('title')
    {{ __('Dashboard') }}
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-6">
            <h2>{{ __('Next events') }}</h2>
            @include('events.shared.event_list', [
                'events' => $events,
                'showVisibility' => false,
                'noEventsMessage' => __('There are no public future events.'),
            ])
        </div>
        @if($bookings !== null)
            <div class="col-12 col-md-6">
                <h2>{{ __('My bookings') }}</h2>
                <div class="list-group">
                    @foreach($bookings as $booking)
                        @php
                            $event = $booking->bookingOption->event;
                        @endphp
                        <a href="{{ route('bookings.show', $booking) }}" class="list-group-item list-group-item-action">
                            <strong>{{ $event->name }}</strong>
                            @isset($event->description)
                                <div class="text-muted">{{ $event->description }}</div>
                            @endisset
                            <div>
                                <i class="fa fa-fw fa-clock"></i>
                                @include('events.shared.event_dates')
                            </div>
                            <div>
                                <i class="fa fa-fw fa-location-pin"></i>
                                {{ $event->location->nameOrAddress }}
                            </div>
                            <div>
                                <i class="fa fa-fw fa-user-alt"></i>
                                {{ $booking->first_name }} {{ $booking->last_name }}
                            </div>
                            <div>
                                @isset($booking->price)
                                    <x-bs::badge variant="primary">{{ formatDecimal($booking->price) }}&nbsp;€</x-bs::badge>
                                    @isset($booking->paid_at)
                                        <x-bs::badge variant="success">{{ __('paid') }} ({{ $booking->paid_at->isMidnight()
                                            ? formatDate($booking->paid_at)
                                            : formatDateTime($booking->paid_at) }})</x-bs::badge>
                                    @else
                                        <x-bs::badge variant="danger">{{ __('not paid yet') }}</x-bs::badge>
                                    @endisset
                                @else
                                    <x-bs::badge variant="primary">{{ __('free of charge') }}</x-bs::badge>
                                @endisset
                                @isset($booking->booked_at)
                                    <x-bs::badge variant="primary">{{ formatDateTime($booking->booked_at) }}</x-bs::badge>
                                @endisset
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
@endsection
