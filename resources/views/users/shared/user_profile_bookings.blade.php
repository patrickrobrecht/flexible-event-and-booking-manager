@php
    /** @var \App\Models\User $user */
    /** @var string $allBookingsLink */
@endphp
<div id="bookings" class="col-12 col-xl-6 col-xxl-4">
    <h2><i class="fa fa-fw fa-file-contract"></i> <a href="{{ $allBookingsLink }}">{{ __('Bookings') }}</a></h2>
    @if($user->bookings->count() === 0)
        <x-bs::alert class="danger">{{ __(':name does not have any bookings yet.', [
            'name' => $user->first_name,
        ]) }}</x-bs::alert>
    @endif
    @include('bookings.shared.booking_list', [
        'bookings' => $user->bookings,
    ])
</div>
