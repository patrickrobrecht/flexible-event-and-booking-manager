@extends('layouts.app')

@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Booking[] $bookings */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events */
@endphp

@section('title')
    {{ __('Dashboard') }}
@endsection

@section('content')
    @include('account.shared.unverified_email')

    <div class="row">
        <div class="col-12 col-md-6">
            <h2><i class="fa fa-fw fa-calendar-days"></i> {{ __('Next events') }}</h2>
            @include('events.shared.event_list', [
                'events' => $events,
                'showVisibility' => false,
                'noEventsMessage' => __('There are no public future events.'),
            ])
        </div>
        @if($bookings !== null)
            <div class="col-12 col-md-6 mt-3 mt-md-0">
                <h2><i class="fa fa-fw fa-file-contract"></i> <a href="{{ route('account.bookings') }}">{{ __('My bookings') }}</a></h2>
                @if($bookings->isEmpty())
                    <x-bs::alert variant="info">
                        {{ __('You do not have any bookings yet.') }}
                    </x-bs::alert>
                @else
                    @include('bookings.shared.booking_list')
                @endif
            </div>
        @endif
    </div>
@endsection
