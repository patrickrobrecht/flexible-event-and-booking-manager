@php
    /** @var \App\Models\EventSeries $eventSeries */
    $showVisibility = $showVisibility ?? true;
    $showParentEventSeries = $showParentEventSeries ?? true;
@endphp
@if($showVisibility)
    <x-badge.visibility :visibility="$eventSeries->visibility"/>
@endif
@if($showParentEventSeries && isset($eventSeries->parentEventSeries))
    <x-bs::badge variant="primary">
        <span><i class="fa fa-fw fa-calendar-week"></i> {{ __('Part of the event series') }}</span>
        @can('view', $eventSeries->parentEventSeries)
            <a class="link-light" href="{{ $eventSeries->parentEventSeries->getRoute() }}">{{ $eventSeries->parentEventSeries->name }}</a>
        @else
            {{ $eventSeries->parentEventSeries->name }}
        @endcan
    </x-bs::badge>
@endisset
<x-bs::badge variant="primary">
    <span><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}:</span>
    @can('view', $eventSeries->organization)
        <a class="link-light" href="{{ $eventSeries->organization->getRoute() }}">{{ $eventSeries->organization->name }}</a>
    @else
        {{ $eventSeries->organization->name }}
    @endcan
</x-bs::badge>
