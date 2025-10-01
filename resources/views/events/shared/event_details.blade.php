@php
    /** @var \App\Models\Event $event */
@endphp

<x-bs::list>
    @isset($event->description)
        <x-bs::list.item>{{ $event->description }}</x-bs::list.item>
    @endisset
    <x-bs::list.item class="d-flex">
        <span class="me-3"><i class="fa fa-fw fa-clock" title="{{ __('Date') }}"></i></span>
        @include('events.shared.event_dates')
    </x-bs::list.item>
    <x-bs::list.item class="d-flex">
        <span class="me-3">
            <i class="fa fa-fw fa-location-pin" title="{{ __('Address') }}"></i>
        </span>
        <div>
            @foreach($event->location->fullAddressBlock as $line)
                {{ $line }}@if(!$loop->last)
                    <br>
                @endif
            @endforeach
        </div>
    </x-bs::list.item>
    @isset($event->website_url)
        <x-bs::list.item class="d-flex">
            <span class="me-3"><i class="fa fa-fw fa-display"></i></span>
            <a href="{{ $event->website_url }}" target="_blank">{{ __('Website') }}</a>
        </x-bs::list.item>
    @endisset
    <x-bs::list.item class="d-flex">
        <span class="me-3"><i class="fa fa-fw fa-sitemap" title="{{ __('Organizations') }}"></i></span>
        <div>
            @can('view', $event->organization)
                <a href="{{ route('organizations.show', $event->organization) }}">{{ $event->organization->name }}</a>
            @else
                {{ $event->organization->name }}
            @endcan
            <div class="d-flex">
                <span class="me-3"><i class="fa fa-fw fa-location-pin" title="{{ __('Address of organization') }}"></i></span>
                <div>
                    @foreach($event->organization->location->fullAddressBlock as $line)
                        <div>{{ $line }}</div>
                    @endforeach
                </div>
            </div>
            @isset($event->organization->phone)
                <div class="d-flex">
                    <span class="me-3"><i class="fa fa-fw fa-phone"></i></span>
                    <a href="{{ $event->organization->phone_link }}">{{ $event->organization->phone }}</a>
                </div>
            @endisset
            @isset($event->organization->email)
                <div class="d-flex">
                    <span class="me-3"><i class="fa fa-fw fa-at"></i></span>
                    <a href="mailto:{{ $event->organization->email }}">{{ $event->organization->email }}</a>
                </div>
            @endisset
        </div>
    </x-bs::list.item>
</x-bs::list>
