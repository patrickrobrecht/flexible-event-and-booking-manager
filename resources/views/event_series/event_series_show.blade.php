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
        <x-bs::breadcrumb.item>>{{ __('Event series') }}</x-bs::breadcrumb.item>
    @endcan
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
        <div class="col-12 col-md-6">
            <h2>{{ __('Events') }}</h2>
            @include('event_series.shared.events_in_series')
        </div>
        @if($eventSeries->subEventSeries->count() > 0 || Auth::user()->can('createChild', $eventSeries))
            <div class="col-12 col-md-6">
                <h2>{{ __('Event series') }}</h2>

                <x-bs::list container="div">
                    @foreach($eventSeries->subEventSeries as $subEventSeries)
                        <x-bs::list.item container="a" variant="action" href="{{ route('event-series.show', $subEventSeries->slug) }}">
                            <strong>{{ $subEventSeries->name }}</strong>
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

    </div>

    <x-text.updated-human-diff :model="$eventSeries"/>
@endsection
