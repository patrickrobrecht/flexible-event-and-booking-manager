@php
    /** @var \App\Models\Organization $organization */
@endphp

<x-bs::list>
    <x-bs::list.item class="d-flex">
        <span class="me-3"><i class="fa fa-fw fa-location-pin" title="{{ __('Address') }}"></i></span>
        <div>
            @foreach($organization->location->fullAddressBlock as $line)
                {{ $line }}@if(!$loop->last)
                    <br>
                @endif
            @endforeach
        </div>
    </x-bs::list.item>
    @isset($organization->register_entry)
        <x-bs::list.item class="d-flex">
            <span class="me-3"><i class="fa fa-fw fa-scale-balanced" title="{{ __('Register entry') }}"></i></span>
            <div>{{ $organization->register_entry }}</div>
        </x-bs::list.item>
    @endisset
    @isset($organization->phone)
        <x-bs::list.item class="d-flex">
            <span class="me-3"><i class="fa fa-fw fa-phone"></i></span>
            <span>{{ $organization->phone }}</span>
        </x-bs::list.item>
    @endisset
    @isset($organization->email)
        <x-bs::list.item class="d-flex">
            <span class="me-3"><i class="fa fa-fw fa-at"></i></span>
            <span><a href="mailto:{{ $organization->email }}">{{ $organization->email }}</a></span>
        </x-bs::list.item>
    @endisset
    @isset($organization->website_url)
        <x-bs::list.item class="d-flex">
            <span class="me-3"><i class="fa fa-fw fa-display"></i></span>
            <a href="{{ $organization->website_url }}" target="_blank">{{ __('Website') }}</a>
        </x-bs::list.item>
    @endisset
    @isset($organization->iban)
        <x-bs::list.item class="d-flex">
            <span class="me-3"><i class="fa fa-fw fa-credit-card"></i></span>
            <div>
                {{ __('Bank account details') }}:
                <div>{{ __('Account holder') }}: {{ $organization->bank_account_holder ?? $organization->name }}</div>
                <div><abbr title="{{ __('International Bank Account Number') }}">IBAN</abbr>: {{ $organization->iban }}</div>
                <div>{{ $organization->bank_name }}</div>
            </div>
        </x-bs::list.item>
    @endisset
</x-bs::list>
