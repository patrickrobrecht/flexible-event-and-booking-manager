@php
    /** @var \App\Models\Booking $booking */
@endphp
@can('viewPDF', $booking)
    <x-bs::button.link variant="secondary" href="{{ route('bookings.show-pdf', $booking) }}">
        <i class="fa fa-file-pdf"></i> {{ __('PDF') }}
    </x-bs::button.link>
@endcan
@can('delete', $booking)
    <x-bs::button variant="danger" form="delete-{{ $booking->id }}">
        <i class="fa fa-fw fa-trash"></i> {{ __('Delete') }}
    </x-bs::button>
    <x-bs::form id="delete-{{ $booking->id }}" method="DELETE"
                action="{{ route('bookings.delete', $booking) }}"/>
@elsecan('restore', $booking)
    <x-bs::button variant="success" form="restore-{{ $booking->id }}">
        <i class="fa fa-fw fa-trash-can-arrow-up"></i> {{ __('Restore') }}
    </x-bs::button>
    <x-bs::form id="restore-{{ $booking->id }}" method="PATCH"
                action="{{ route('bookings.restore', $booking) }}"/>
@endcan
