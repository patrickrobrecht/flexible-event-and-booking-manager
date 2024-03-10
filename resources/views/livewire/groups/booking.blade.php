<x-bs::list.item class="draggable-item"
                 draggable="true"
                 wire:key="{{ 'booking' . $booking->id }}"
                 x-on:dragstart="dragStart($event, {{ $booking->id }})">
    <div>
        <strong>{{ $booking->first_name }} {{ $booking->last_name }}</strong>
        <small>({{ $booking->bookingOption->name }})</small>
    </div>
    <div>
        <span class="text-nowrap">
            <i class="fa fa-fw fa-clock" title="{{ __('Booking date') }}"></i>
            @isset($booking->booked_at)
                {{ formatDateTime($booking->booked_at) }}
            @else
                <x-bs::badge variant="danger">{{ __('Booking not completed yet') }}</x-bs::badge>
            @endisset
        </span>
    </div>
</x-bs::list.item>
