@php
    /** @var \App\Models\Organization $organization */
@endphp

<x-bs::list>
    @isset($organization->website_url)
        <x-bs::list.item class="d-flex">
            <span class="me-3"><i class="fa fa-fw fa-display"></i></span>
            <a href="{{ $organization->website_url }}" target="_blank">{{ __('Website') }}</a>
        </x-bs::list.item>
    @endisset
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
    @isset($organization->representatives)
        <x-bs::list.item class="d-flex">
            <span class="me-3"><i class="fa fa-fw fa-user-friends" title="{{ __('Representatives') }}"></i></span>
            <div>{{ $organization->representatives }}</div>
        </x-bs::list.item>
    @endisset
    @isset($organization->register_entry)
            <x-bs::list.item class="d-flex">
            <span class="me-3"><i class="fa fa-fw fa-scale-balanced" title="{{ __('Register entry') }}"></i></span>
            <div>{{ $organization->register_entry }}</div>
        </x-bs::list.item>
    @endisset
</x-bs::list>
