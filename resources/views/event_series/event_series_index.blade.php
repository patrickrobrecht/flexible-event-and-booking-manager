@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\EventSeries[] $eventSeries */
@endphp

@section('title')
    {{ __('Event series') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    <x-bs::button.group>
        @can('create', \App\Models\EventSeries::class)
            <x-button.create href="{{ route('event-series.create') }}">
                {{ __('Create event series') }}
            </x-button.create>
        @endcan
    </x-bs::button.group>

    <x-form.filter>
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field id="name" name="filter[name]" type="text"
                                  :from-query="true">{{ __('Name') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\EventSeries::sortOptions()->getNamesWithLabels()"
                                  :from-query="true">{{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$eventSeries->total()"/>

    <div class="row my-3">
        @foreach($eventSeries as $eventSeriesItem)
            <div class="col-12 col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <a href="{{ route('event-series.show', $eventSeriesItem->slug) }}">{{ $eventSeriesItem->name }}</a>
                        </h2>
                    </div>
                    <x-bs::list :flush="true">
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-eye" title="{{ __('Visibility') }}"></i>
                            <x-badge.visibility :visibility="$eventSeriesItem->visibility"/>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            @isset($eventSeriesItem->parentEventSeries)
                                <span class="text-nowrap"><i class="fa fa-fw fa-calendar-week"></i> {{ __('Part of the event series') }}</span>
                                <x-slot:end>
                                    <a href="{{ route('event-series.show', $eventSeriesItem->parentEventSeries->slug) }}">{{ $eventSeriesItem->parentEventSeries->name }}</a>
                                </x-slot:end>
                            @else
                                <span class="text-nowrap">
                                    <i class="fa fa-fw fa-calendar-week"></i>
                                    @can('viewAny', [\App\Models\Document::class, $eventSeriesItem])
                                        <a href="{{ route('event-series.show', $eventSeriesItem->slug) }}#series">{{ __('Event series') }}</a>
                                    @else
                                        {{ __('Event series') }}
                                    @endcan
                                </span>
                                <x-slot:end>
                                    <x-bs::badge>{{ formatInt($eventSeriesItem->sub_event_series_count) }}</x-bs::badge>
                                </x-slot:end>
                            @endisset
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span class="text-nowrap">
                                <i class="fa fa-fw fa-calendar-days"></i>
                                @can('viewAny', [\App\Models\Document::class, $eventSeriesItem])
                                    <a href="{{ route('event-series.show', $eventSeriesItem->slug) }}#events">{{ __('Events') }}</a>
                                @else
                                    {{ __('Events') }}
                                @endcan
                            </span>
                            <x-slot:end>
                                <x-bs::badge>{{ formatInt($eventSeriesItem->events_count) }}</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span class="text-nowrap">
                                <i class="fa fa-fw fa-file"></i>
                                @can('viewAny', [\App\Models\Document::class, $eventSeriesItem])
                                    <a href="{{ route('event-series.show', $eventSeriesItem->slug) }}#documents">{{ __('Documents') }}</a>
                                @else
                                    {{ __('Documents') }}
                                @endcan
                            </span>
                            <x-slot:end>
                                <x-bs::badge>{{ formatInt($eventSeriesItem->documents_count) }}</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                    </x-bs::list>
                    <div class="card-body">
                        @can('update', $eventSeriesItem)
                            <x-button.edit href="{{ route('event-series.edit', $eventSeriesItem) }}"/>
                        @endcan
                        @can('createChild', $eventSeriesItem)
                            <x-button.create href="{{ route('event-series.create', ['parent_event_series_id' => $eventSeriesItem->id]) }}">
                                {{ __('Create event series') }}
                            </x-button.create>
                        @endcan
                    </div>
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$eventSeriesItem"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $eventSeries->withQueryString()->links() }}
@endsection
