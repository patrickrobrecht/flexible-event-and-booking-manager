<x-bs::list :flush="true" data-group-id="{{ $groupId }}">
    @foreach($event->getBookingOptions() as $bookingOption)
        @php
            $bookingsForOption = $bookings->filter(fn (\App\Models\Booking $booking) => $booking->booking_option_id === $bookingOption->id);
            $averageAge = $averageAge = $bookingsForOption->average('age');
        @endphp
        @if(in_array($bookingOption->id, $bookingOptionIds, true) && $bookingsForOption->count() > 0)
            <x-bs::list.item variant="primary">
                {{ $bookingOption->name }} ({{ formatInt($bookingsForOption->count()) }})
                @isset($averageAge)
                    <x-slot:end>
                        <x-bs::badge>{{ formatTransChoiceDecimal(':count years', $averageAge, 1) }}</x-bs::badge>
                    </x-slot:end>
                @endisset
            </x-bs::list.item>
            @foreach($bookingsForOption as $booking)
                <x-bs::list.item class="draggable-item" draggable="true"
                                 wire:key="{{ 'booking' . $booking->id }}"
                                 x-on:dragstart="dragStart($event, {{ $booking->id }})">
                    <div class="d-flex justify-content-between align-items-center">
                        <span>
                            @can('view', $booking)
                                <a class="fw-bold" href="{{ route('bookings.show', $booking) }}" target="_blank">{{ $booking->first_name }} {{ $booking->last_name }}</a>
                            @else
                                <strong>{{ $booking->first_name }} {{ $booking->last_name }}</strong>
                            @endcan
                            @isset($event->parentEvent)
                                @php
                                    $group = $booking->getGroup($event->parentEvent);
                                @endphp
                                @isset($group)
                                    ({{ $group->name }})
                                @endisset
                            @endisset
                        </span>
                        @isset($booking->age)
                            <span>
                                <x-bs::badge>{{ formatTransChoiceDecimal(':count years', $booking->age, 1) }}</x-bs::badge>
                            </span>
                        @endisset
                    </div>
                    <div class="text-nowrap">
                        <i class="fa fa-fw fa-clock" title="{{ __('Booking date') }}"></i>
                        @isset($booking->booked_at)
                            {{ formatDateTime($booking->booked_at) }}
                        @else
                            <x-bs::badge variant="danger">{{ __('Booking not completed yet') }}</x-bs::badge>
                        @endisset
                    </div>
                </x-bs::list.item>
            @endforeach
        @endif
    @endforeach
</x-bs::list>
