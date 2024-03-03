<x-bs::list.item class="draggable-item"
                 draggable="true"
                 x-on:dragstart="dragStart($event, {{ $booking->id }})">
    {{ $booking->first_name }} {{ $booking->last_name }}
    <x-slot:end>
        <span class="badge bg-secondary rounded-pill">{{ formatDate($booking->booked_at) }}</span>
    </x-slot:end>
</x-bs::list.item>
