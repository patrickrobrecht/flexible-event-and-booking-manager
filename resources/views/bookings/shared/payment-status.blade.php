@php
    /** @var \App\Models\Booking $booking */
@endphp
@isset($booking->price)
    <x-bs::badge :variant="$booking->price === $booking->bookingOption->price ? 'primary' : 'secondary'">{{ formatDecimal($booking->price) }}&nbsp;€</x-bs::badge>
    @can('viewPaymentStatus', $booking)
        @isset($booking->paid_at)
            <x-bs::badge variant="success">{{ __('paid') }} ({{ $booking->paid_at->isMidnight()
                ? formatDate($booking->paid_at)
                : formatDateTime($booking->paid_at) }})</x-bs::badge>
        @else
            <x-bs::badge variant="danger">{{ __('not paid yet') }}</x-bs::badge>
            @if($booking->status === \App\Enums\BookingStatus::Confirmed && isset($booking->payment_deadline))
                <span @class([
                    'text-nowrap',
                    'text-danger' => $booking->payment_deadline->isPast(),
                ])>({{ __('Payment deadline') }}: {{ formatDate($booking->payment_deadline) }})</span>
            @endif
        @endisset
    @endcan
@else
    <x-bs::badge variant="primary">{{ __('free of charge') }}</x-bs::badge>
@endisset
