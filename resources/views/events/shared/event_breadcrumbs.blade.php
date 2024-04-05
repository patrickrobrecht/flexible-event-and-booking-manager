@php
    /** @var ?\App\Models\Event $event */
    $parentEvent = $event->parentEvent ?? null;
@endphp
@can('viewAny', \App\Models\Event::class)
    <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
@else
    <x-bs::breadcrumb.item>{{ __('Events') }}</x-bs::breadcrumb.item>
@endcan
@isset($parentEvent)
    @can('view', $parentEvent)
        <x-bs::breadcrumb.item href="{{ route('events.show', $parentEvent) }}">{{ $parentEvent->name }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ $parentEvent->name }}</x-bs::breadcrumb.item>
    @endcan
@endisset
@isset($event)
    @can('view', $event)
        <x-bs::breadcrumb.item href="{{ route('events.show', $event) }}">{{ $event->name }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ $event->name }}</x-bs::breadcrumb.item>
    @endcan
@endisset
