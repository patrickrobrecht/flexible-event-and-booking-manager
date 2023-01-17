@extends('layouts.app')

@php
    /** @var \App\Models\BookingOption $bookingOption */
@endphp

@section('title')
    {{ $bookingOption->event->name }}: {{ $bookingOption->name }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('events.index') }}">{{ __('Events') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb href="{{ route('events.show', $bookingOption->event) }}">{{ $bookingOption->event->name }}</x-nav.breadcrumb>
    <x-nav.breadcrumb>{{ $bookingOption->name }}</x-nav.breadcrumb>
@endsection

@section('headline-buttons')
    @can('update', $bookingOption)
        <x-button.edit href="{{ route('booking-options.edit', [$event, $bookingOption]) }}"/>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-4">
            @include('events.shared.event_details')
        </div>
        <div class="col-12 col-md-8">
            @auth()
                @if($bookingOption->isRestrictedBy(\App\Options\BookingRestriction::VerifiedEmailAddressRequired) && Auth::user()?->email_verified_at === null)
                    <p class="alert alert-danger">
                        {{ __('Bookings are only available for logged-in users with a verified email address.') }}
                        <a href="{{ route('verification.notice') }}" class="alert-link">{{ __('Verify e-mail address') }}</a>
                    </p>
                @endif
            @else
                @if($bookingOption->isRestrictedBy(\App\Options\BookingRestriction::AccountRequired))
                    <p class="alert alert-danger">
                        {{ __('Bookings are only available for logged-in users.') }}
                        <a href="{{ route('login') }}" class="alert-link">{{ __('Login') }}</a>
                    </p>
                @else
                    <p class="alert alert-danger">
                        {{ __('To be able to view bookings after submission, we recommend logging in or registering beforehand.') }}
                        {{ __('This is the only way we can assign your registration to your account and offer you additional functions such as the reuse of entries for the next booking or updating bookings in case of changes.') }}
                    </p>
               @endif
            @endauth

            @if(!isset($bookingOption->available_from) || $bookingOption->available_from->isFuture())
                <p class="alert alert-danger">
                    {{ __('Bookings are not possible yet.') }}
                </p>
            @elseif(isset($bookingOption->available_until) && $bookingOption->available_until->isPast())
                <p class="alert alert-danger">
                    {{ __('The booking period ended at :date.', ['date' => formatDateTime($bookingOption->available_until)]) }}
                    {{ __('Bookings are not possible anymore.') }}
                </p>
            @elseif($bookingOption->hasReachedMaximumBookings())
                <p class="alert alert-danger">
                    {{ __('The maximum number of bookings has been reached.') }}
                    {{ __('Bookings are not possible anymore.') }}
                </p>
            @else
                @include('layouts.alerts')

                @can('book', $bookingOption)
                    <x-form method="POST" action="{{ route('bookings.store', [$event, $bookingOption]) }}">
                        @include('bookings.booking_form_fields', [
                            'booking' => null,
                            'bookingOption' => $bookingOption,
                        ])

                        @if(isset($bookingOption->price) && $bookingOption->price)
                            <div class="alert alert-info">
                                {{ __('Please transfer :price to the following bank account:', [
                                    'price' => formatDecimal($bookingOption->price) . ' €',
                                ]) }}
                                <ul>
                                    <li>IBAN: {{ config('app.bank_account.iban') }}</li>
                                    <li>{{ __('Bank') }}: {{ config('app.bank_account.bank_name') }}</li>
                                    <li>{{ __('Account holder') }}: {{ config('app.bank_account.holder') }}</li>
                                </ul>
                            </div>
                        @endif

                        <x-button.save>
                            @isset($bookingOption->price)
                                {{ __('Book with costs') }}
                                ({{ formatDecimal($bookingOption->price) }}&nbsp;€)
                            @else
                                {{ __('Book') }}
                            @endisset
                        </x-button.save>
                    </x-form>
                @endcan
            @endif
        </div>
    </div>
@endsection
