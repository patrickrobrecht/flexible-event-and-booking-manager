@php
    /** @var \App\Models\BookingOption $bookingOption */
    /** @var ?\App\Models\Booking $booking */
    $price = $booking->price ?? $bookingOption->price;
    /** @var \App\Models\Event $event */
    $organization = $event->organization;
@endphp

@if($price !== null)
    <x-bs::alert>
        {{ __('Please transfer :price to the following bank account by :date:', [
            'price' => formatDecimal($price) . ' €',
            'date' => formatDate($bookingOption->getPaymentDeadline($booking->booked_at ?? null)),
        ]) }}
        <ul>
            <li>{{ __('Account holder') }}: {{ $organization->bank_account_holder ?? $organization->name }}</li>
            <li>IBAN: {{ $organization->iban }}</li>
            <li>{{ __('Bank') }}: {{ $organization->bank_name }}</li>
        </ul>
    </x-bs::alert>
@endif
