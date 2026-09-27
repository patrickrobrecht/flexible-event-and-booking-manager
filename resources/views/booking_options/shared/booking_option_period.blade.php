@php
    /** @var ?\App\Models\BookingOption $bookingOption */
@endphp

@isset($bookingOption->available_from)
    <div class="{{ $class ?? '' }}">
        {{ __('Booking period') }}:
        <strong class="text-nowrap">{{ formatDateTime($bookingOption->available_from) }}</strong>
        <strong class="text-nowrap">
            @isset($bookingOption->available_until)
                {{ __('until :end', ['end' => formatDateTime($bookingOption->available_until)]) }}
            @else
                {{ __('forever') }}
            @endisset
        </strong>
    </div>
@endisset
