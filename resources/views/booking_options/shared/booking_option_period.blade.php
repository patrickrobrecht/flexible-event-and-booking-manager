@php
    /** @var ?\App\Models\BookingOption $bookingOption */
@endphp

@isset($bookingOption->available_from)
    <div class="small text-muted">
        {{ __('Booking period') }}:
        {{ formatDateTime($bookingOption->available_from) }}
        -
        @isset($bookingOption->available_until)
            {{ formatDateTime($bookingOption->available_until) }}
        @else
            {{ __('forever') }}
        @endisset
    </div>
@endisset
