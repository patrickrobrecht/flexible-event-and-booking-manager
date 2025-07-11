@extends('layouts.app')

@php
    /** @var \App\Models\Booking $booking */
    $bookingOption = $booking->bookingOption;
    $event = $bookingOption->event;
@endphp

@section('title')
    {{ __('Edit booking no. :id', [
        'id' => $booking->id,
    ]) }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('events.show', $event) }}">{{ $event->name }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ $bookingOption->name }}</x-bs::breadcrumb.item>
    @can('viewBookings', $bookingOption)
        <x-bs::breadcrumb.item href="{{ route('bookings.index', [$event, $bookingOption]) }}">{{ __('Bookings') }}</x-bs::breadcrumb.item>
    @endcan
    <x-bs::breadcrumb.item>{{ __('Booking no. :id', [
        'id' => $booking->id,
    ]) }}</x-bs::breadcrumb.item>
@endsection

@section('headline')
    <h1>{{ $event->name }}: {{ $bookingOption->name }}</h1>
@endsection

@section('headline-buttons')
    @can('view', $booking)
        <x-bs::button.link variant="secondary" href="{{ route('bookings.show', $booking) }}">
            <i class="fa fa-eye"></i> {{ __('View') }}
        </x-bs::button.link>
    @endcan
    @include('bookings.shared.booking_buttons')
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-4">
            @include('events.shared.event_details')
        </div>
        <div class="col-12 col-md-8 mt-3 mt-md-0">
            @include('bookings.shared.booking_details')

            <x-bs::form method="PUT" action="{{ route('bookings.update', $booking) }}" enctype="multipart/form-data">
                @canany(['updateBookingComment', 'updatePaymentStatus'], $booking)
                    <div class="row">
                        @can('updateBookingComment', $booking)
                            <div class="col-12 col-md-6">
                                <x-bs::form.field name="comment" type="textarea"
                                                  :value="$booking->comment ?? null">{{ __('Comment') }}</x-bs::form.field>
                            </div>
                        @endcan
                        @can('updatePaymentStatus', $booking)
                            <div class="col-12 col-md-6">
                                <x-bs::form.field name="paid_at" type="datetime-local"
                                                  :value="$booking->paid_at ?? null">{{ __('Paid at') }}</x-bs::form.field>
                            </div>
                        @endcan
                    </div>
                @endcanany

                @include('bookings.booking_form_fields', [
                    'booking' => $booking,
                    'bookingOption' => $bookingOption,
                    'canEdit' => true,
                ])

                <div class="d-flex flex-wrap gap-1">
                    <x-bs::button>
                        <i class="fa fa-fw fa-save"></i> {{ __('Save') }}
                    </x-bs::button>
                    <x-bs::button.link variant="danger" href="{{ route('bookings.index', [$event, $bookingOption]) }}">
                        <i class="fa fa-fw fa-window-close"></i> {{ __('Discard') }}
                    </x-bs::button.link>
                </div>
            </x-bs::form>
        </div>
    </div>

    <x-text.timestamp :model="$booking ?? null"/>
@endsection
