@extends('layouts.app')

@php
    use Portavice\Bladestrap\Support\Options;
    /** @var ?\App\Models\EventSeries $eventSeries */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\EventSeries[] $allEventSeries */

    /** @var ?\App\Models\EventSeries $parentEventSeries */
    $parentEventSeries = $eventSeries->parentEventSeries ?? null;
    if ($parentEventSeries === null) {
        $parentEventSeriesId = (int) \Portavice\Bladestrap\Support\ValueHelper::getFromQueryOrDefault('parent_event_series_id');
        $parentEventSeries = $allEventSeries->firstWhere('id', '=', $parentEventSeriesId);
    }

    $isCreateForm = \Illuminate\Support\Facades\Request::routeIs('event-series.create');
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

@section('headline-buttons')
    @isset($eventSeries)
        @can('forceDelete', $eventSeries)
            <x-form.delete-modal :id="$eventSeries->id"
                                 :name="$eventSeries->name"
                                 :route="route('event-series.destroy', $eventSeries)"/>
        @endcan
    @endisset
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
                                  :options="\App\Enums\Visibility::toOptions()"
                                  :value="$eventSeries->visibility->value ?? null"><i class="fa fa-fw fa-eye"></i> {{ __('Visibility') }}</x-bs::form.field>
                <x-bs::form.field name="organization_id" type="radio"
                                  :options="Options::fromModels($organizations, 'name')"
                                  :value="$eventSeries->organization_id ?? null"
                                  :from-query="$isCreateForm"><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}</x-bs::form.field>
                <x-bs::form.field name="parent_event_series_id" type="select"
                                  :options="Options::fromModels($allEventSeries->except($eventSeries->id ?? null), 'name')->prepend(__('none'), '')"
                                  :value="$eventSeries->parent_event_series_id ?? null"
                                  :from-query="$isCreateForm"><i class="fa fa-fw fa-calendar-days"></i> {{ __('Part of the event series') }}</x-bs::form.field>

                <section class="my-3">
                    <h2><i class="fa fa-fw fa-list-check"></i> {{ __('Responsibilities') }}</h2>
                    @livewire('users.search-users', [
                        'selectedUsers' => $eventSeries->responsibleUsers ?? \Illuminate\Database\Eloquent\Collection::empty(),
                    ])
                </section>

                <x-button.group-save :show-create="!isset($eventSeries)"
                                     :index-route="route('event-series.index')"/>
            </x-bs::form>
        </div>

        @isset($eventSeries)
            <div class="col-12 col-md-6">
                <h2><i class="fa fa-fw fa-calendar-days"></i> {{ __('Events') }}</h2>
                @include('event_series.shared.events_in_series')
            </div>
        @endisset
    </div>

    <x-text.timestamp :model="$eventSeries ?? null"/>
@endsection
