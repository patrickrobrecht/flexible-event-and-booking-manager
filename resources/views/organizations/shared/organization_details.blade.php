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
            <span><a href="{{ $organization->phone_link }}">{{ $organization->phone }}</a></span>
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
    @can('viewAny', \App\Models\Material::class)
        <x-bs::list.item>
            <span class="me-3"><i class="fa fa-fw fa-toolbox"></i></span>
            <a href="{{ route('materials.index', ['filter[organization_id]' => $organization->id]) }}">{{ __('Materials') }}</a>
        </x-bs::list.item>
    @endcan
    @can('viewAny', \App\Models\Event::class)
        <x-bs::list.item>
            <span class="me-3"><i class="fa fa-fw fa-calendar-days"></i></span>
            <a href="{{ route('events.index', ['filter[organization_id]' => $organization->id]) }}">{{ __('Events') }}</a>
        </x-bs::list.item>
    @endcan
    @can('viewAny', \App\Models\EventSeries::class)
        <x-bs::list.item>
            <span class="me-3"><i class="fa fa-fw fa-calendar-week"></i></span>
            <a href="{{ route('event-series.index', ['filter[organization_id]' => $organization->id]) }}">{{ __('Event series') }}</a>
        </x-bs::list.item>
    @endcan
</x-bs::list>
