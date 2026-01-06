@if($booking->trashed())
    <x-bs::alert variant="danger" class="fw-bold mt-3">
        <i class="fa fa-warning"></i> {{ __('This booking has been deleted.') }}
    </x-bs::alert>
@endif

<div class="row">
    <div class="col-12 col-lg-6 vstack mb-3">
        <div>
            <x-bs::badge variant="light"><i class="fa fw-fw fa-hashtag"></i> {{ $booking->id }}</x-bs::badge>
        </div>
        <div>
            <i class="fa fa-fw fa-clock" title="{{ __('Booking date') }}"></i>
            @isset($booking->booked_at)
                {{ formatDateTime($booking->booked_at) }}
            @else
                <x-bs::badge variant="danger">{{ __('Booking not completed yet') }}</x-bs::badge>
            @endisset
        </div>
        <div>
            <i class="fa fa-fw fa-user" title="{{ __('Booked by') }}"></i>
            @isset($booking->bookedByUser)
                @can('view', $booking->bookedByUser)
                    <a href="{{ route('users.show', $booking->bookedByUser) }}">{{ $booking->bookedByUser->name }}</a>
                @else
                    {{ $booking->bookedByUser->name }}
                @endcan
            @else
                {{ __('Guest') }}
            @endisset
        </div>
        @can('updateBookingComment', $booking)
            <div>
                <i class="fa fa-fw fa-comment" title="{{ __('Comment') }}"></i>
                <span>{{ $booking->comment ?? '—' }}</span>
            </div>
        @endcan
        <div>
            <i class="fa fa-fw fa-euro" title="{{ __('Price') }}"></i> @include('bookings.shared.payment-status')
            <div class="mt-3">
                @include('booking_options.shared.booking_option_payment')
            </div>
        </div>
    </div>
    @can('viewGroups', $booking->bookingOption->event)
        @if($booking->groups->isNotEmpty())
            @php
                $groups = $booking->groups
                    ->filter(fn (\App\Models\Group $group) => \Illuminate\Support\Facades\Auth::user()?->can('view', $group->event))
                    ->sortBy(fn (\App\Models\Group $group) => [
                        $group->event->is($booking->bookingOption->event) ? 0 : 1,
                        $group->event->name,
                    ]);
            @endphp
            <div class="col-12 col-lg-6">
                <h2><i class="fa fa-fw fa-people-group"></i> {{ __('Groups') }}</h2>
                <x-bs::list class="mb-3">
                    @foreach($groups as $group)
                        <x-bs::list.item>
                            @can('viewGroups', $group->event)
                                <a href="{{ route('groups.index', $event) }}">{{ $group->name }}</a>
                            @else
                                {{ $group->name }}
                            @endcan
                            @if($group->event->isNot($booking->bookingOption->event))
                               <x-slot:end>
                                   <a href="{{ route('events.show', $group->event) }}">{{ $group->event->name }}</a>
                               </x-slot:end>
                            @endif
                        </x-bs::list.item>
                    @endforeach
                </x-bs::list>
            </div>
        @endif
    @endcan
</div>
