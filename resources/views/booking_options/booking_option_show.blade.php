@extends('layouts.app')

@php
    /** @var \App\Models\Event $event */
    /** @var \App\Models\BookingOption $bookingOption */

    $loggedInUser = \Illuminate\Support\Facades\Auth::user();
@endphp

@section('title')
    {{ $event->name }}: {{ $bookingOption->name }}
@endsection

@section('breadcrumbs')
    @include('events.shared.event_breadcrumbs')
    <x-bs::breadcrumb.item>{{ $bookingOption->name }}</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $bookingOption)
        <x-button.edit href="{{ route('booking-options.edit', [$event, $bookingOption]) }}"/>
    @endcan
@endsection

@section('content')
    @include('events.shared.event_badges')

    <div class="row my-3">
        <div class="col-12 col-lg-4">
            @include('events.shared.event_details')
        </div>
        <div class="col-12 col-lg-8 pt-3 pt-lg-0">
            @php
                $canBookResponse = \Illuminate\Support\Facades\Gate::inspect('book', $bookingOption);
                $canUpdate = \Illuminate\Support\Facades\Auth::user()?->can('update', $bookingOption);
            @endphp
            @if($canBookResponse->denied())
                @if($canUpdate)
                    <x-bs::alert variant="info" class="fw-bolder">{{ __('Because you can edit the booking option, you can see a preview of the booking form here, although bookings are not currently possible.') }}</x-bs::alert>
                @endif
                <x-bs::alert variant="danger">
                    {{ $canBookResponse->message() }}
                    @auth
                        @if($loggedInUser?->email_verified_at === null && $bookingOption->isRestrictedBy(\App\Enums\BookingRestriction::VerifiedEmailAddressRequired))
                            {{ __('Your e-mail address :email has not been verified yet.', [
                                'email' => $loggedInUser->email,
                            ]) }}
                            <x-bs::form method="POST" action="{{ route('verification.send') }}">
                                <button class="btn btn-link stretched-link ps-0 fw-bold">{{ __('Send verification link via e-mail') }}</button>
                            </x-bs::form>
                        @endif
                    @else
                        @if($bookingOption->isRestrictedBy(\App\Enums\BookingRestriction::AccountRequired))
                            {{ __('Bookings are only available for logged-in users.') }}
                            <a href="{{ route('login') }}" class="alert-link">{{ __('Login') }}</a>
                        @endif
                    @endauth
                </x-bs::alert>
            @endif
            @if($canUpdate || $canBookResponse->allowed())
                @include('layouts.alerts')

                @guest
                    @if(!$bookingOption->isRestrictedBy(\App\Enums\BookingRestriction::AccountRequired) && !$bookingOption->isRestrictedBy(\App\Enums\BookingRestriction::VerifiedEmailAddressRequired))
                        <x-bs::alert variant="warning">
                            {{ __('To be able to view bookings after submission, we recommend logging in or registering beforehand.') }}
                            {{ __('This is the only way we can assign your registration to your account and offer you additional functions such as the reuse of entries for the next booking or updating bookings in case of changes.') }}
                        </x-bs::alert>
                    @endif
                @endguest
                @if($bookingOption->isRestrictedBy(\App\Enums\BookingRestriction::OnlySelf))
                    <x-bs::alert variant="warning">
                        {{ __('You may only register yourself for this event. It is not allowed to fill out this registration form on behalf of other people.') }}
                    </x-bs::alert>
                @endif

                <x-bs::form method="POST" action="{{ route('bookings.store', [$event, $bookingOption]) }}" enctype="multipart/form-data">
                    @include('bookings.booking_form_fields', [
                        'booking' => null,
                        'bookingOption' => $bookingOption,
                        'canEdit' => $canBookResponse->allowed(),
                    ])

                    @include('booking_options.shared.booking_option_payment')

                    <x-button.save :disabled="$canBookResponse->denied()">
                        @isset($bookingOption->price)
                            {{ __('Book with costs') }}
                            ({{ formatDecimal($bookingOption->price) }}&nbsp;€)
                        @else
                            {{ __('Book') }}
                        @endisset
                    </x-button.save>
                </x-bs::form>
            @endif
        </div>
    </div>
@endsection
