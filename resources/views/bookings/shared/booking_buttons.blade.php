@php
    /** @var \App\Models\Booking $booking */
@endphp
@can('viewPDF', $booking)
    <x-bs::button.link variant="secondary" href="{{ route('bookings.show-pdf', $booking) }}">
        <i class="fa fa-file-pdf"></i> {{ __('PDF') }}
    </x-bs::button.link>
@endcan
@can('delete', $booking)
    <x-button.delete form="delete-{{ $booking->id }}"/>
    <x-bs::form id="delete-{{ $booking->id }}" method="DELETE"
                action="{{ route('bookings.delete', $booking) }}"/>
@elsecan('restore', $booking)
    <x-button.restore form="restore-{{ $booking->id }}"/>
    <x-bs::form id="restore-{{ $booking->id }}" method="PATCH"
                action="{{ route('bookings.restore', $booking) }}"/>
@endcan
