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
    <x-nav.breadcrumb href="{{ route('event-series.index') }}">{{ __('Event series') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-form method="{{ isset($eventSeries) ? 'PUT' : 'POST' }}"
            action="{{ isset($eventSeries) ? route('event-series.update', $eventSeries) : route('event-series.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input name="name" type="text"
                                  :value="$eventSeries->name ?? null"/>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="slug">{{ __('Slug') }}</x-form.label>
                    <x-form.input name="slug" type="text" aria-describedby="slugHint"
                                  :value="$eventSeries->slug ?? null"/>
                    <div id="slugHint" class="form-text">
                        {!! __('This field defines the path in the URL, such as :url. If you leave it empty, is auto-generated for you.', [
                            'url' => isset($eventSeries->slug)
                                ? sprintf('<a href="%s" target="_blank">%s</a>', route('event-series.show', $eventSeries), route('event-series.show', $eventSeries, false))
                                : '<strong>' . route('event-series.show', Str::of(__('Name of the event series'))->snake('-')) . '</strong>'
                        ]) !!}
                    </div>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="visibility">{{ __('Visibility') }}</x-form.label>
                    <x-form.select name="visibility"
                                   :options="\App\Options\Visibility::keysWithNames()"
                                   :value="$eventSeries->visibility->value ?? null" />
                </x-form.row>
            </div>

            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="parent_event_series_id">{{ __('Part of the event series') }}</x-form.label>
                    <x-form.select name="parent_event_series_id"
                                   :options="$allEventSeries->except($eventSeries->id ?? null)->pluck('name', 'id')"
                                   :value="$eventSeries->parent_event_series_id ?? null">
                        <option value="">{{ __('none') }}</option>
                    </x-form.select>
                </x-form.row>

                @isset($eventSeries)
                    <h2>{{ __('Events') }}</h2>
                    @include('event_series.shared.events_in_series')
                @endisset
            </div>
        </div>

        <x-button.group>
            <x-button.save>
                @isset($eventSeries)
                    {{ __( 'Save' ) }}
                @else
                    {{ __('Create') }}
                @endisset
            </x-button.save>
            <x-button.cancel href="{{ route('event-series.index') }}"/>
        </x-button.group>
    </x-form>

    <x-text.timestamp :model="$eventSeries ?? null"/>
@endsection
