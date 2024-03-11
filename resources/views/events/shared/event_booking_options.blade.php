@php
    /** @var \App\Models\Event $event */
@endphp
@foreach($event->bookingOptions as $bookingOption)
    <x-bs::list.item>
        <div>
            <a href="{{ route('booking-options.show', [$event, $bookingOption]) }}" class="fw-bold">{{ $bookingOption->name }}</a>
            <x-bs::badge variant="primary">
                @isset($bookingOption->price)
                    {{ formatDecimal($bookingOption->price) }}&nbsp;€
                @else
                    {{ __('free of charge') }}
                @endisset
            </x-bs::badge>
        </div>
        @isset($bookingOption->description)
            <p class="lead">{{ $bookingOption->description }}</p>
        @endisset
        @include('booking_options.shared.booking_option_period')
        @canany(['book', 'viewBookings', 'update'], $bookingOption)
            <x-bs::button.group class="mt-3">
                @can('book', $bookingOption)
                    <x-button.create href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ __('Book') }}</x-button.create>
                @endcan
                @can('viewBookings', $bookingOption)
                    <x-bs::button.link variant="secondary" href="{{ route('bookings.index', [$event, $bookingOption]) }}">
                        <i class="fa fa-fw fa-file-contract"></i> {{ __('Bookings') }}
                        <x-bs::badge variant="danger">{{ formatInt($bookingOption->bookings_count ?? 0) }}&nbsp;/&nbsp;{{
                            isset($bookingOption->maximum_bookings)
                                ? formatInt($bookingOption->maximum_bookings)
                                : __('unlimited')
                        }}</x-bs::badge>
                    </x-bs::button.link>
                @endcan
                @can('update', $bookingOption)
                    <x-button.edit href="{{ route('booking-options.edit', [$event, $bookingOption]) }}"/>
                @endcan
            </x-bs::button.group>
        @endcanany
    </x-bs::list.item>
@endforeach
