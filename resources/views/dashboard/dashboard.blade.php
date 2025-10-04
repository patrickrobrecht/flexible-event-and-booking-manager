@extends('layouts.app')

@php
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Booking[] $bookings */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Event[] $events */
@endphp

@section('title')
    {{ __('Dashboard') }}
@endsection

@section('content')
    @auth
        @php
            $loggedInUser = \Illuminate\Support\Facades\Auth::user();
        @endphp
        @if($loggedInUser->email_verified_at === null)
            <x-bs::alert variant="danger">
                {{ __('Your e-mail address :email has not been verified yet.', [
                    'email' => $loggedInUser->email,
                ]) }}
                <x-bs::form method="POST" action="{{ route('verification.send') }}">
                    <button class="btn btn-link stretched-link ps-0 fw-bold">{{ __('Send verification link via e-mail') }}</button>
                </x-bs::form>
            </x-bs::alert>
        @endif
    @endauth
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
                <h2><i class="fa fa-fw fa-file-contract"></i>{{ __('My bookings') }}</h2>
                @include('bookings.shared.booking_list')
            </div>
        @endif
    </div>
@endsection
