@php
    /** @var \App\Models\Event $event */
    $showVisibility = $showVisibility ?? true;
    $showParentEvent = $showParentEvent ?? true;
    $showSeries = $showSeries ?? true;
@endphp
@if($showVisibility)
    <x-badge.enum :case="$event->visibility"/>
@endif
@if($showParentEvent && isset($event->parentEvent))
    <x-bs::badge variant="primary">
        <span><i class="fa fa-fw fa-calendar-days"></i>{{ __('Part of the event') }}</span>
        <a class="link-light" href="{{ $event->parentEvent->getRoute() }}">{{ $event->parentEvent->name }}</a>
    </x-bs::badge>
@endif
@if($showSeries && isset($event->eventSeries))
    <x-bs::badge variant="primary">
        <span><i class="fa fa-fw fa-calendar-week"></i> {{ __('Part of the event series') }}</span>
        @can('view', $event->eventSeries)
            <a class="link-light" href="{{ $event->eventSeries->getRoute() }}">{{ $event->eventSeries->name }}</a>
        @else
            {{ $event->eventSeries->name }}
        @endcan
    </x-bs::badge>
@endif
<x-bs::badge variant="primary">
    <span><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}:</span>
    @can('view', $event->organization)
        <a class="link-light" href="{{ $event->organization->getRoute() }}">{{ $event->organization->name }}</a>
    @else
        {{ $event->organization->name }}
    @endcan
</x-bs::badge>
