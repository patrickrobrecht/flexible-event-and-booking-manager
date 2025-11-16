@php
    /** @var \App\Models\Location $location */
    /** @var bool $flush */
@endphp

<x-bs::list :flush="$flush">
    <x-bs::list.item>
        <i class="fa fa-fw fa-road"></i>
        <span class="d-inline-block">
            <div class="d-flex flex-column">
                @foreach($location->addressBlock as $line)
                    <div>{{ $line }}</div>
                @endforeach
            </div>
        </span>
    </x-bs::list.item>
    @isset($location->website_url)
        <x-bs::list.item>
            <i class="fa fa-fw fa-display"></i>
            <a href="{{ $location->website_url }}" target="_blank">{{ __('Website') }}</a>
        </x-bs::list.item>
    @endisset
    <x-bs::list.item>
        <span>
            <i class="fa fa-fw fa-calendar-days"></i>
            <a href="{{ route('events.index', ['filter[location_id]' => $location->id, 'filter[event_type]' => \App\Enums\EventType::MainEvent]) }}" target="_blank">
                {{ __('Main events') }}
            </a>
        </span>
        <x-slot:end>
            <x-bs::badge>{{ formatInt($location->main_events_count) }}</x-bs::badge>
        </x-slot:end>
    </x-bs::list.item>
    <x-bs::list.item>
        <span>
            <i class="fa fa-fw fa-calendar-days"></i>
            <a href="{{ route('events.index', ['filter[location_id]' => $location->id]) }}" target="_blank">
                {{ __('Events') }}
            </a>
        </span>
        <x-slot:end>
            <x-bs::badge>{{ formatInt($location->events_count) }}</x-bs::badge>
        </x-slot:end>
    </x-bs::list.item>
    <x-bs::list.item>
        <span>
            <i class="fa fa-fw fa-sitemap"></i>
            <a href="{{ route('organizations.index', ['filter[location_id]' => $location->id]) }}" target="_blank">
                {{ __('Organizations') }}
            </a>
        </span>
        <x-slot:end>
            <x-bs::badge>{{ formatInt($location->organizations_count) }}</x-bs::badge>
        </x-slot:end>
    </x-bs::list.item>
</x-bs::list>
