@extends('layouts.app')

@php
    /** @var ?\App\Models\EventSeries $eventSeries */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\EventSeries[] $allEventSeries */

    /** @var ?\App\Models\EventSeries $parentEventSeries */
    $parentEventSeries = $eventSeries->parentEventSeries ?? null;
    if ($parentEventSeries === null) {
        $parentEventSeriesId = (int) \Portavice\Bladestrap\Support\ValueHelper::getFromQueryOrDefault('parent_event_series_id');
        $parentEventSeries = $allEventSeries->firstWhere('id', '=', $parentEventSeriesId);
    }
@endphp

@section('title')
    @isset($eventSeries)
        {{ __('Edit :name', ['name' => $eventSeries->name]) }}
    @else
        {{ __('Create event series') }}
    @endisset
@endsection

@section('breadcrumbs')
    @include('event_series.shared.event_series_breadcrumbs')
@endsection

@section('content')
    <div class="row">
        <div class="col-12 col-md-6">
            <x-bs::form method="{{ isset($eventSeries) ? 'PUT' : 'POST' }}"
                        action="{{ isset($eventSeries) ? route('event-series.update', $eventSeries) : route('event-series.store') }}">
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
                                  :value="$eventSeries->visibility->value ?? null"><i class="fa fa-fw fa-eye"></i> {{ __('Visibility') }}</x-bs::form.field>
                <x-bs::form.field name="parent_event_series_id" type="select"
                                  :options="\Portavice\Bladestrap\Support\Options::fromModels($allEventSeries->except($eventSeries->id ?? null), 'name')->prepend(__('none'), '')"
                                  :value="$eventSeries->parent_event_series_id ?? null"
                                  :from-query="\Illuminate\Support\Facades\Request::routeIs('event-series.create')"><i class="fa fa-fw fa-calendar-days"></i> {{ __('Part of the event series') }}</x-bs::form.field>

                <section class="my-3">
                    <h2>{{ __('Organization team') }}</h2>
                    @livewire('users.search-users', [
                        'fieldName' => 'responsible_user_id',
                        'selectedUsers' => $eventSeries->responsibleUsers ?? \Illuminate\Database\Eloquent\Collection::empty(),
                    ])
                </section>

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
        </div>

        @isset($eventSeries)
            <div class="col-12 col-md-6">
                <h2>{{ __('Events') }}</h2>
                @include('event_series.shared.events_in_series')
            </div>
        @endisset
    </div>

    <x-text.timestamp :model="$eventSeries ?? null"/>
@endsection
