@php
    /** @var \App\Models\Booking $booking */
@endphp
@isset($booking->price)
    {{ formatDecimal($booking->price) }}&nbsp;€
    @can('viewPaymentStatus', $booking)
        @isset($booking->paid_at)
            <x-bs::badge variant="success">{{ __('paid') }} ({{ $booking->paid_at->isMidnight()
                ? formatDate($booking->paid_at)
                : formatDateTime($booking->paid_at) }})</x-bs::badge>
        @else
            <x-bs::badge variant="danger">{{ __('not paid yet') }}</x-bs::badge>
        @endisset
    @endcan
@else
    <x-bs::badge variant="primary">{{ __('free of charge') }}</x-bs::badge>
@endisset
