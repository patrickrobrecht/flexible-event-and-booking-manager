@php
    /** @var Booking $booking */
    /** @var bool $showGroups */
@endphp
<div class="card avoid-break">
    <div @class([
        'card-header',
        'text-bg-danger' => $booking->trashed(),
    ])>
        <h2 class="card-title">
            @can('view', $booking)
                <a href="{{ route('bookings.show', $booking) }}">{{ $booking->first_name }} {{ $booking->last_name }}</a>
            @else
                {{ $booking->first_name }} <strong>{{ $booking->last_name }}</strong>
            @endcan
        </h2>
        <div class="card-subtitle">
            <x-bs::badge variant="light"><i class="fa fw-fw fa-hashtag"></i> {{ $booking->id }}</x-bs::badge>
            <x-badge.enum :case="$booking->status"/>
        </div>
    </div>
    <x-bs::list :flush="true">
        @if($showEvent)
            @php
                $event = $booking->bookingOption->event;
                $location = $event->location;
            @endphp
            <x-bs::list.item>
                <strong>{{ $booking->bookingOption->name }}</strong>
                <div>
                    <i class="fa fa-fw fa-calendar-days"></i>
                    @can('view', $event)
                        <a href="{{ route('events.show', $event) }}">{{ $event->name }}</a>
                    @else
                        {{ $event->name }}
                    @endcan
                </div>
                <div>
                    <i class="fa fa-fw fa-clock"></i>
                    @include('events.shared.event_dates')
                </div>
                <div>
                    <i class="fa fa-fw fa-location-pin"></i>
                    @can('view', $location)
                        <a href="{{ route('locations.show', $location) }}">{{ $location->nameOrAddress }}</a>
                    @else
                        {{ $location->nameOrAddress }}
                    @endcan
                </div>
            </x-bs::list.item>
        @endif
        @if($showGroups)
            @php
                $group = $booking->getGroup($event);
            @endphp
            <x-bs::list.item>
                <i class="fa fa-fw fa-people-group" title="{{ __('Group') }}"></i>
                @isset($group)
                    <strong>{{ $group->name }}</strong>
                @else
                    <strong class="text-danger">{{ __('none') }}</strong>
                @endisset
            </x-bs::list.item>
        @endif
        <x-bs::list.item>
            <i class="fa fa-fw fa-clock" title="{{ __('Booking date') }}"></i>
            @isset($booking->booked_at)
                {{ formatDateTime($booking->booked_at) }}
            @else
                <x-bs::badge variant="danger">{{ __('Booking not completed yet') }}</x-bs::badge>
            @endisset
        </x-bs::list.item>
        <x-bs::list.item>
            <i class="fa fa-fw fa-user" title="{{ __('Booked by') }}"></i>
            @isset($booking->bookedByUser)
                <span title="{{ $booking->bookedByUser->email }}">
                                    @can('view', $booking->bookedByUser)
                        <a href="{{ route('users.show', $booking->bookedByUser) }}">{{ $booking->bookedByUser->name }}</a>
                    @else
                        {{ $booking->bookedByUser->name }}
                    @endcan
                                </span>
                @isset($booking->bookedByUser->email_verified_at)
                    <x-bs::badge variant="success">{{ __('verified') }}</x-bs::badge>
                @else
                    <x-bs::badge variant="danger">{{ __('not verified') }}</x-bs::badge>
                @endisset
            @else
                {{ __('Guest') }}
            @endisset
        </x-bs::list.item>
        @can('updateBookingComment', $booking)
            <x-bs::list.item>
                <i class="fa fa-fw fa-comment" title="{{ __('Comment') }}"></i>
                <span>{{ $booking->comment ?? '—' }}</span>
            </x-bs::list.item>
        @endcan
        <x-bs::list.item>
            <i class="fa fa-fw fa-euro" title="{{ __('Price') }}"></i>
            @include('bookings.shared.payment-status')
        </x-bs::list.item>
        @isset($booking->date_of_birth)
            <x-bs::list.item>
                <span class="text-nowrap"><i class="fa fa-fw fa-cake-candles" title="{{ __('Date of birth') }}"></i></span>
                <span>
                                    <span class="me-2">{{ formatDate($booking->date_of_birth) }}</span>
                                    @isset($booking->age)
                        <x-bs::badge>{{ formatTransChoiceDecimal(':count years', $booking->age, 1) }}</x-bs::badge>
                    @endisset
                                </span>
            </x-bs::list.item>
        @endisset
        <x-bs::list.item>
            <i class="fa fa-fw fa-at"></i>
            <a href="mailto:{{ $booking->email }}">{{ $booking->email }}</a>
        </x-bs::list.item>
        <x-bs::list.item>
            <i class="fa fa-fw fa-phone"></i>
            @isset($booking->phone)
                <a href="{{ $booking->phone_link }}">{{ $booking->phone }}</a>
            @else
                {{ __('none') }}
            @endisset
        </x-bs::list.item>
        <x-bs::list.item>
            <i class="fa fa-fw fa-road"></i>
            <span class="d-inline-block">
                                <div class="d-flex flex-column">
                                    @if($booking->hasAnyFilledAddressField())
                                        <div>{{ $booking->streetLine }}</div>
                                        <div>{{ $booking->cityLine }}</div>
                                        <div>{{ $booking->country }}</div>
                                    @else
                                        {{ __('none') }}
                                    @endif
                                </div>
                            </span>
        </x-bs::list.item>
    </x-bs::list>
    @canany(['viewPDF', 'update', 'delete', 'restore'], $booking)
        <div class="card-body d-flex flex-wrap gap-1 d-print-none">
            @can('viewPDF', $booking)
                <x-bs::button.link variant="secondary" href="{{ route('bookings.show-pdf', $booking) }}" class="text-nowrap">
                    <i class="fa fa-file-pdf"></i> {{ __('PDF') }}
                </x-bs::button.link>
            @endcan
            @can('update', $booking)
                <x-button.edit href="{{ route('bookings.edit', $booking) }}" class="text-nowrap"/>
            @endcan
            @can('delete', $booking)
                <x-bs::button variant="danger" form="delete-{{ $booking->id }}">
                    <i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}
                </x-bs::button>
            @elsecan('restore', $booking)
                <x-bs::button variant="success" form="restore-{{ $booking->id }}">
                    <i class="fa fa-fw fa-trash-can-arrow-up"></i> {{ __('Restore') }}
                </x-bs::button>
            @endcan
            @can('delete', $booking)
                <x-bs::form id="delete-{{ $booking->id }}" method="DELETE"
                            action="{{ route('bookings.delete', $booking) }}"/>
            @elsecan('restore', $booking)
                <x-bs::form id="restore-{{ $booking->id }}" method="PATCH"
                            action="{{ route('bookings.restore', $booking) }}"/>
            @endcan
        </div>
    @endcan
    <div class="card-footer">
        <x-text.updated-human-diff :model="$booking"/>
    </div>
</div>
