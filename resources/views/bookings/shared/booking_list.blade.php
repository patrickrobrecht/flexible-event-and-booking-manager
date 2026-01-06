<x-bs::list>
    @foreach($bookings as $booking)
        @php
            $event = $booking->bookingOption->event;
        @endphp
        <x-bs::list.item container="a" href="{{ route('bookings.show', $booking) }}" variant="action">
            <div>
                <i class="fa fa-fw fa-user-alt"></i>
                <strong>{{ $booking->first_name }} {{ $booking->last_name }}</strong>
            </div>
            <div>
                <i class="fa fa-fw fa-calendar-days"></i>
                {{ $event->name }}
            </div>
            <div>
                <i class="fa fa-fw fa-clock"></i>
                @include('events.shared.event_dates')
            </div>
            <div>
                <i class="fa fa-fw fa-location-pin"></i>
                {{ $event->location->nameOrAddress }}
            </div>
            <div class="mt-1">
                <x-bs::badge variant="light"><i class="fa fw-fw fa-hashtag"></i> {{ $booking->id }}</x-bs::badge>
                @isset($booking->booked_at)
                    <x-bs::badge variant="light">{{ formatDateTime($booking->booked_at) }}</x-bs::badge>
                @endisset
                @isset($booking->price)
                    <x-bs::badge :variant="$booking->price === $booking->bookingOption->price ? 'primary' : 'secondary'">{{ formatDecimal($booking->price) }}&nbsp;€</x-bs::badge>
                    @isset($booking->paid_at)
                        <x-bs::badge variant="success">{{ __('paid') }} ({{ $booking->paid_at->isMidnight()
                            ? formatDate($booking->paid_at)
                            : formatDateTime($booking->paid_at) }})</x-bs::badge>
                    @else
                        <x-bs::badge variant="danger">{{ __('not paid yet') }}</x-bs::badge>
                    @endisset
                @else
                    <x-bs::badge variant="primary">{{ __('free of charge') }}</x-bs::badge>
                @endisset
            </div>
        </x-bs::list.item>
    @endforeach
</x-bs::list>
