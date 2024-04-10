@extends('layouts.app')

@php
    /** @var \App\Models\EventSeries $eventSeries */
@endphp

@section('title')
    {{ $eventSeries->name }}
@endsection

@section('breadcrumbs')
    @can('viewAny', \App\Models\EventSeries::class)
        <x-bs::breadcrumb.item href="{{ route('event-series.index') }}">{{ __('Event series') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('Event series') }}</x-bs::breadcrumb.item>
    @endcan
    @isset($eventSeries->parentEventSeries)
        <x-bs::breadcrumb.item href="{{ route('event-series.show', $eventSeries->parentEventSeries) }}">{{ $eventSeries->parentEventSeries->name }}</x-bs::breadcrumb.item>
    @endisset
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $eventSeries)
        <x-button.edit href="{{ route('event-series.edit', $eventSeries) }}"/>
    @endcan
@endsection

@section('content')
    @isset($eventSeries->parentEventSeries)
        <x-bs::badge variant="primary">
            <span>
                <i class="fa fa-fw fa-calendar-week"></i>
                {{ __('Part of the event series') }}
            </span>
            <a href="{{ route('event-series.show', $eventSeries->parentEventSeries->slug) }}" class="link-light">
                {{ $eventSeries->parentEventSeries->name }}
            </a>
        </x-bs::badge>
    @endisset

    <div class="row my-3">
        <div id="events" class="col-12 col-xl-6 col-xxl-4">
            <h2>{{ __('Events') }}</h2>
            @include('event_series.shared.events_in_series')
            @can('create', \App\Models\Event::class)
                <div class="my-3">
                    <x-button.create href="{{ route('events.create', ['event_series_id' => $eventSeries->id]) }}">
                        {{ __('Create event') }}
                    </x-button.create>
                </div>
            @endcan
        </div>
        @php
            $subEventSeriesList = $eventSeries->subEventSeries
                ->filter(fn (\App\Models\EventSeries $subEventSeries) => \Illuminate\Support\Facades\Gate::check('view', $subEventSeries));
            $hasSubEventSeriesToShow = $subEventSeriesList->count() > 0 || Auth::user()?->can('createChild', $eventSeries);
        @endphp
        @if($hasSubEventSeriesToShow)
            <div id="series" class="col-12 col-xl-6 col-xxl-4 mt-4 mt-xl-0">
                <h2>{{ __('Event series') }}</h2>
                <x-bs::list container="div">
                    @foreach($subEventSeriesList as $subEventSeries)
                        <x-bs::list.item container="a" variant="action" href="{{ route('event-series.show', $subEventSeries->slug) }}">
                            <div>
                                <strong>{{ $subEventSeries->name }}</strong>
                                @isset($subEventSeries->events_min_started_at, $subEventSeries->events_max_started_at)
                                    <div>
                                        <i class="fa fa-fw fa-clock"></i>
                                        @if($subEventSeries->events_min_started_at->isSameDay($subEventSeries->events_max_started_at))
                                            {{ formatDate($subEventSeries->events_min_started_at) }}
                                        @else
                                            {{ formatDate($subEventSeries->events_min_started_at) }} / {{ formatDate($subEventSeries->events_max_started_at) }}
                                        @endif
                                    </div>
                                @endisset
                            </div>
                            <x-slot:end>
                                <x-bs::badge>{{ formatTransChoice(':count events', $subEventSeries->events_count) }}</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                    @endforeach
                </x-bs::list>
                @can('createChild', $eventSeries)
                    <div class="mt-3">
                        <x-button.create href="{{ route('event-series.create', ['parent_event_series_id' => $eventSeries->id]) }}">
                            {{ __('Create event series') }}
                        </x-button.create>
                    </div>
                @endcan
            </div>
        @endif
        @canany(['viewAny', 'create'], [\App\Models\Document::class, $eventSeries])
            <div id="documents" @class([
                'col-12 col-xl-6 col-xxl-4',
                'mt-4 mt-xxl-0' => $hasSubEventSeriesToShow,
            ])>
                <h2>{{ __('Documents') }}</h2>
            @can('viewAny', [\App\Models\Document::class, $eventSeries])
                @include('documents.shared.document_list', [
                    'documents' => $eventSeries->documents,
                ])
            @endcan
            @include('documents.shared.document_add_modal', [
                'reference' => $eventSeries,
                'routeForAddDocument' => route('event-series.documents.store', $eventSeries),
            ])
        @endcanany
    </div>

    @can('update', $eventSeries)
        <x-text.updated-human-diff :model="$eventSeries"/>
    @endcan
@endsection
