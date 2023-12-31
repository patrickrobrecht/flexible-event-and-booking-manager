@extends('layouts.app')

@php
    /** @var \App\Models\BookingOption $bookingOption */
@endphp

@section('title')
    {{ $bookingOption->event->name }}: {{ $bookingOption->name }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('events.show', $bookingOption->event) }}">{{ $bookingOption->event->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item>{{ $bookingOption->name }}</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $bookingOption)
        <x-button.edit href="{{ route('booking-options.edit', [$event, $bookingOption]) }}"/>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-lg-4">
            @include('events.shared.event_details')
        </div>
        <div class="col-12 col-lg-8 pt-3 pt-lg-0">
            @auth
                @if($bookingOption->isRestrictedBy(\App\Options\BookingRestriction::VerifiedEmailAddressRequired) && Auth::user()?->email_verified_at === null)
                    <x-bs::alert variant="danger">
                        {{ __('Bookings are only available for logged-in users with a verified email address.') }}
                        <a href="{{ route('verification.notice') }}" class="alert-link">{{ __('Verify e-mail address') }}</a>
                    </x-bs::alert>
                @endif
            @else
                @if($bookingOption->isRestrictedBy(\App\Options\BookingRestriction::AccountRequired))
                    <x-bs::alert variant="danger">
                        {{ __('Bookings are only available for logged-in users.') }}
                        <a href="{{ route('login') }}" class="alert-link">{{ __('Login') }}</a>
                    </x-bs::alert>
                @else
                    <x-bs::alert variant="danger">
                        {{ __('To be able to view bookings after submission, we recommend logging in or registering beforehand.') }}
                        {{ __('This is the only way we can assign your registration to your account and offer you additional functions such as the reuse of entries for the next booking or updating bookings in case of changes.') }}
                    </x-bs::alert>
                @endif
            @endauth

            @php
                $canBookResponse = \Illuminate\Support\Facades\Gate::inspect('book', $bookingOption);
                $canUpdate = \Illuminate\Support\Facades\Auth::user()?->can('update', $bookingOption);
            @endphp
            @if($canBookResponse->denied())
                <x-bs::alert variant="danger">{{ $canBookResponse->message() }}</x-bs::alert>
                @if($canUpdate)
                    <x-bs::alert variant="info" class="fw-bolder">{{ __('Because you can edit the booking option, you can see a preview of the booking form here, although bookings are not currently possible.') }}</x-bs::alert>
                @endif
            @endif
            @if($canBookResponse->allowed() || $canUpdate)
                @include('layouts.alerts')

                <x-bs::form method="POST" action="{{ route('bookings.store', [$event, $bookingOption]) }}" enctype="multipart/form-data">
                    @include('bookings.booking_form_fields', [
                        'booking' => null,
                        'bookingOption' => $bookingOption,
                        'canEdit' => $canBookResponse->allowed(),
                    ])

                    @if(isset($bookingOption->price) && $bookingOption->price)
                        <x-bs::alert>
                            {{ __('Please transfer :price to the following bank account:', [
                                'price' => formatDecimal($bookingOption->price) . ' €',
                            ]) }}
                            <ul>
                                <li>IBAN: {{ config('app.bank_account.iban') }}</li>
                                <li>{{ __('Bank') }}: {{ config('app.bank_account.bank_name') }}</li>
                                <li>{{ __('Account holder') }}: {{ config('app.bank_account.holder') }}</li>
                            </ul>
                        </x-bs::alert>
                    @endif

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
