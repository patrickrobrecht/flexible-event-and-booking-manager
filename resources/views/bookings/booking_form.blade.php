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
    @can('viewAny', \App\Models\Booking::class)
        <x-bs::breadcrumb.item href="{{ route('bookings.index', [$event, $bookingOption]) }}">{{ __('Bookings') }}</x-bs::breadcrumb.item>
    @endcan
@endsection

@section('headline')
    <h1>{{ $event->name }}: {{ $bookingOption->name }}</h1>
@endsection

@section('headline-buttons')
    @can('viewPDF', $booking)
        <x-bs::button.link variant="secondary" href="{{ route('bookings.show-pdf', $booking) }}">
            <i class="fa fa-file-pdf"></i>
            {{ __('PDF') }}
        </x-bs::button.link>
    @endcan
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-4">
            @include('events.shared.event_details')
        </div>
        <div class="col-12 col-md-8">
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

                <x-bs::button.group>
                    <x-button.save>
                        @isset($booking)
                            {{ __( 'Save' ) }}
                        @else
                            {{ __('Create') }}
                        @endisset
                    </x-button.save>
                    <x-button.cancel href="{{ route('bookings.index', [$event, $bookingOption]) }}"/>
                </x-bs::button.group>
            </x-bs::form>
        </div>
    </div>

    <x-text.timestamp :model="$booking ?? null"/>
@endsection
