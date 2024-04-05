@php
    /** @var ?\App\Models\EventSeries $eventSeries */
    $parentEventSeries = $eventSeries->parentEventSeries ?? null;
@endphp
<x-bs::breadcrumb.item href="{{ route('event-series.index') }}">{{ __('Event series') }}</x-bs::breadcrumb.item>
@isset($parentEventSeries)
    @can('view', $parentEventSeries)
        <x-bs::breadcrumb.item href="{{ route('event-series.show', $parentEventSeries) }}">{{ $parentEventSeries->name }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ $parentEventSeries->name }}</x-bs::breadcrumb.item>
    @endif
@endisset
@isset($eventSeries)
    @can('view', $eventSeries)
        <x-bs::breadcrumb.item href="{{ route('event-series.show', $eventSeries) }}">{{ $eventSeries->name }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ $eventSeries->name }}</x-bs::breadcrumb.item>
    @endcan
@endisset
