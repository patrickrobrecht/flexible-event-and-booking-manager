@extends('layouts.app')

@php
    /** @var ?\App\Models\EventSeries $eventSeries */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\EventSeries[] $allEventSeries */
@endphp

@section('title')
    @isset($eventSeries)
        {{ __('Edit :name', ['name' => $eventSeries->name]) }}
    @else
        {{ __('Create event series') }}
    @endisset
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item href="{{ route('event-series.index') }}">{{ __('Event series') }}</x-bs::breadcrumb.item>
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    <x-bs::form method="{{ isset($eventSeries) ? 'PUT' : 'POST' }}"
                action="{{ isset($eventSeries) ? route('event-series.update', $eventSeries) : route('event-series.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-bs::form.field name="name" type="text"
                                  :value="$eventSeries->name ?? null">{{ __('Name') }}</x-bs::form.field>
                <x-bs::form.field name="slug" type="text" aria-describedby="slugHint"
                                  :value="$eventSeries->slug ?? null">
                    {{ __('Slug') }}
                    <x-slot:hint>
                        {!! __('This field defines the path in the URL, such as :url. If you leave it empty, is auto-generated for you.', [
                            'url' => isset($eventSeries->slug)
                                ? sprintf('<a href="%s" target="_blank">%s</a>', route('event-series.show', $eventSeries), route('event-series.show', $eventSeries, false))
                                : '<strong>' . route('event-series.show', Str::of(__('Name of the event series'))->snake('-')) . '</strong>'
                        ]) !!}
                    </x-slot:hint>
                </x-bs::form.field>
                <x-bs::form.field name="visibility" type="select"
                                  :options="\App\Options\Visibility::toOptions()"
                                  :value="$eventSeries->visibility->value ?? null">{{ __('Visibility') }}</x-bs::form.field>
                <x-bs::form.field name="parent_event_series_id" type="select"
                                  :options="\Portavice\Bladestrap\Support\Options::fromModels($allEventSeries->except($eventSeries->id ?? null), 'name')->prepend(__('none'), '')"
                                  :value="$eventSeries->parent_event_series_id ?? null"
                                  :from-query="\Illuminate\Support\Facades\Request::routeIs('event-series.create')">{{ __('Part of the event series') }}</x-bs::form.field>
            </div>

            @isset($eventSeries)
                <div class="col-12 col-md-6">
                    <h2>{{ __('Events') }}</h2>
                    @include('event_series.shared.events_in_series')
                </div>
            @endisset
        </div>

        <x-bs::button.group>
            <x-button.save>
                @isset($eventSeries)
                    {{ __( 'Save' ) }}
                @else
                    {{ __('Create') }}
                @endisset
            </x-button.save>
            <x-button.cancel href="{{ route('event-series.index') }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <x-text.timestamp :model="$eventSeries ?? null"/>
@endsection
