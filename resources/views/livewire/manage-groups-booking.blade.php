<x-bs::list.item class="draggable-item"
                 draggable="true"
                 wire:key="{{ 'booking' . $booking->id }}"
                 x-on:dragstart="dragStart($event, {{ $booking->id }})">
    <div>
        {{ $booking->first_name }} {{ $booking->last_name }}
        <div class="small">({{ $booking->bookingOption->name }})</div>
    </div>
    <x-slot:end>
        <span class="badge bg-secondary rounded-pill">{{ formatDate($booking->booked_at) }}</span>
    </x-slot:end>
</x-bs::list.item>
