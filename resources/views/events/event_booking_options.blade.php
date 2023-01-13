@php
    /** @var \App\Models\Event $event */
@endphp
@foreach($event->bookingOptions as $bookingOption)
    <x-list.item>
        <div>
            <a href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ $bookingOption->name }}</a>
            <span class="badge bg-primary">
                @isset($bookingOption->price)
                    {{ formatDecimal($bookingOption->price) }}&nbsp;€
                @else
                    {{ __('free of charge') }}
                @endisset
            </span>
            <span class="badge bg-primary">
                {{ formatInt($bookingOption->bookings_count ?? 0) }}&nbsp;/&nbsp;{{
                    isset($bookingOption->maximum_bookings)
                        ? formatInt($bookingOption->maximum_bookings)
                        : __('unlimited')
                }}
            </span>
            <div class="small text-muted">
                @isset($bookingOption->available_from)
                    {{ formatDateTime($bookingOption->available_from) }}
                @endisset
                -
                @isset($bookingOption->available_until)
                    {{ formatDateTime($bookingOption->available_until) }}
                @else
                    {{ __('forever') }}
                @endisset
            </div>
        </div>
        <x-button.edit href="{{  route('booking-options.edit', [$event, $bookingOption]) }}"/>
    </x-list.item>
@endforeach
