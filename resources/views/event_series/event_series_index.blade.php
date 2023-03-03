@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\EventSeries[] $eventSeries */
@endphp

@section('title')
    {{ __('Event series') }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-button.group>
        @can('create', \App\Models\EventSeries::class)
            <x-button.create href="{{ route('event-series.create') }}">
                {{ __('Create event series') }}
            </x-button.create>
        @endcan
    </x-button.group>

    <x-form.filter method="GET">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input id="name" name="filter[name]"/>
                </x-form.row>
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
                    <x-list.group class="list-group-flush">
                        <x-list.item :flex="false">
                            <i class="fa fa-fw fa-eye" title="{{ __('Visibility') }}"></i>
                            <x-badge.visibility :visibility="$eventSeriesItem->visibility"/>
                        </x-list.item>
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-calendar-week"></i>
                                {{ __('Part of the event series') }}
                            </span>
                            <span>
                                @isset($eventSeriesItem->parentEventSeries)
                                    <a href="{{ route('event-series.show', $eventSeriesItem->parentEventSeries->slug) }}">
                                        {{ $eventSeriesItem->parentEventSeries->name }}
                                    </a>
                                @else
                                    {{ __('none') }}
                                @endif
                            </span>
                        </x-list.item>
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-calendar-days"></i>
                                {{ __('Events') }}
                            </span>
                            <x-badge.counter>{{ formatInt($eventSeriesItem->events_count) }}</x-badge.counter>
                        </x-list.item>
                    </x-list.group>
                    <div class="card-body">
                        @can('update', $eventSeriesItem)
                            <x-button.edit href="{{ route('event-series.edit', $eventSeriesItem) }}"/>
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
