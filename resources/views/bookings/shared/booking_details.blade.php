<div class="vstack mb-3">
    <div>
        <i class="fa fa-fw fa-clock"></i>
        @isset($booking->booked_at)
            {{ formatDateTime($booking->booked_at) }}
        @else
            <span class="badge bg-primary">{{ __('Booking not completed yet') }}</span>
        @endisset
    </div>
    <div>
        <i class="fa fa-fw fa-user"></i>
        @isset($booking->bookedByUser)
            {{ $booking->bookedByUser->first_name }} {{ $booking->bookedByUser->last_name }}
        @else
            {{ __('Guest') }}
        @endisset
    </div>
    <div>
        <i class="fa fa-fw fa-euro"></i>
        @isset($booking->price)
            {{ formatDecimal($booking->price) }}&nbsp;€
        @else
            <span class="badge bg-primary">{{ __('free of charge') }}</span>
        @endisset
    </div>
</div>
