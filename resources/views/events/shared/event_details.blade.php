@php
    /** @var \App\Models\Event $event */
@endphp

<x-bs::list>
    @isset($event->description)
        <x-bs::list.item>{{ $event->description }}</x-bs::list.item>
    @endisset
    @isset($event->website_url)
        <x-bs::list.item>
            <a href="{{ $event->website_url }}" target="_blank">{{ __('Website') }}</a>
        </x-bs::list.item>
    @endisset
    <x-bs::list.item>
        <span class="me-3"><i class="fa fa-fw fa-eye" title="{{ __('Visibility') }}"></i></span>
        <x-badge.visibility :visibility="$event->visibility"/>
    </x-bs::list.item>
    <x-bs::list.item>
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
    <x-bs::list.item class="d-flex">
        <span class="me-3"><i class="fa fa-fw fa-sitemap" title="{{ __('Organizations') }}"></i></span>
        <div>
            @if($event->organizations->count() === 0)
                {{ __('none') }}
            @else
                <ul class="list-unstyled">
                    @foreach($event->organizations as $organization)
                        <li>{{ $organization->name }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    </x-bs::list.item>
</x-bs::list>
