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
            @isset($bookingOption->description)
                <p class="lead">
                    {{ $bookingOption->description }}
                </p>
            @endisset
            @include('booking_options.shared.booking_option_period')
        </div>
        <x-button.group :vertical="true">
            @can('create', $bookingOption)
                <x-button.create href="{{ route('booking-options.show', [$event, $bookingOption]) }}">{{ __('Book') }}</x-button.create>
            @endcan
            @can('viewAny', \App\Models\Booking::class)
                <a class="btn btn-secondary" href="{{ route('bookings.index', [$event, $bookingOption]) }}">
                    <i class="fa fa-file-contract"></i> {{ __('Bookings') }}
                    <span class="badge bg-danger">{{ formatInt($bookingOption->bookings_count ?? 0) }}&nbsp;/&nbsp;{{
                        isset($bookingOption->maximum_bookings)
                            ? formatInt($bookingOption->maximum_bookings)
                            : __('unlimited')
                    }}</span>
                </a>
            @endcan
            @can('update', $bookingOption)
                <x-button.edit href="{{  route('booking-options.edit', [$event, $bookingOption]) }}"/>
            @endcan
        </x-button.group>
    </x-list.item>
@endforeach
