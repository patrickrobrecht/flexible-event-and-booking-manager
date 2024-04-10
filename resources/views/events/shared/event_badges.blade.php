@php
    /** @var \App\Models\Event $event */
    $showVisibility = $showVisibility ?? true;
    $showParentEvent = $showParentEvent ?? true;
    $showSeries = $showSeries ?? true;
@endphp
@if($showVisibility)
    <x-badge.visibility :visibility="$event->visibility"/>
@endif
@if($showParentEvent && isset($event->parentEvent))
    <x-bs::badge variant="primary">
        <span><i class="fa fa-fw fa-calendar-days"></i>{{ __('Part of the event') }}</span>
        <a class="link-light" href="{{ route('events.show', $event->parentEvent) }}">{{ $event->parentEvent->name }}</a>
    </x-bs::badge>
@endif
@if($showSeries && isset($event->eventSeries))
    <x-bs::badge variant="primary">
        <span><i class="fa fa-fw fa-calendar-week"></i> {{ __('Part of the event series') }}</span>
        <a class="link-light" href="{{ route('event-series.show', $event->eventSeries->slug) }}">{{ $event->eventSeries->name }}</a>
    </x-bs::badge>
@endif
