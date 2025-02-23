@php
    /** @var \App\Models\Event $event */
    /** @var \App\Models\BookingOption $bookingOption */
    $organization = $event->organization;
@endphp

@if(isset($bookingOption->price) && $bookingOption->price)
    <x-bs::alert>
        {{ __('Please transfer :price to the following bank account by :date:', [
            'price' => formatDecimal($bookingOption->price) . ' €',
            'date' => formatDate($bookingOption->getPaymentDeadline()),
        ]) }}
        <ul>
            <li>{{ __('Account holder') }}: {{ $organization->bank_account_holder ?? $organization->name }}</li>
            <li>IBAN: {{ $organization->iban }}</li>
            <li>{{ __('Bank') }}: {{ $organization->bank_name }}</li>
        </ul>
    </x-bs::alert>
@endif
