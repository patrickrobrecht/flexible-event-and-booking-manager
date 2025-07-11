@extends('layouts.app')

@php
    use App\Enums\FilterValue;
    use Portavice\Bladestrap\Support\Options;

    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\EventSeries[] $eventSeries */
@endphp

@section('title')
    {{ __('Event series') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    @can('create', \App\Models\EventSeries::class)
        <x-bs::button.link href="{{ route('event-series.create') }}" class="d-print-none">
            <i class="fa fa-fw fa-plus"></i> {{ __('Create event series') }}
        </x-bs::button.link>
    @endcan

    <x-form.filter>
        <div class="row">
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="name" name="filter[name]" type="text"
                                  :from-query="true">{{ __('Name') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="visibility" name="filter[visibility]" type="select"
                                  :options="\App\Enums\Visibility::toOptionsWithAll()"
                                  :from-query="true"><i class="fa fa-fw fa-eye"></i> {{ __('Visibility') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="event_id" name="filter[event_id]" type="select"
                                  :options="Options::fromArray(\App\Models\Event::filterOptions())"
                                  :from-query="true"><i class="fa fa-fw fa-calendar-days"></i> {{ __('Events') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="organization_id" name="filter[organization_id]" type="select"
                                  :options="Options::fromModels($organizations, 'name')->prepend(__('all'), FilterValue::All->value)"
                                  :cast="FilterValue::castToIntIfNoValue()"
                                  :from-query="true"><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="document_id" name="filter[document_id]" type="select"
                                  :options="Options::fromArray(\App\Models\Document::filterOptions())"
                                  :from-query="true"><i class="fa fa-fw fa-file"></i> {{ __('Documents') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="event_series_type" name="filter[event_series_type]" type="select"
                                  :options="\App\Enums\EventSeriesType::toOptionsWithAll()"
                                  :from-query="true"><i class="fa fa-fw fa-calendar-check"></i> {{ __('Event series type') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\EventSeries::sortOptions()->getNamesWithLabels()"
                                  :from-query="true"><i class="fa fa-fw fa-sort"></i> {{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$eventSeries->total()"/>

    <div class="row my-3">
        @foreach($eventSeries as $eventSeriesItem)
            <div class="col-12 col-lg-6 col-xxl-4 mb-3">
                <div class="card avoid-break">
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
                            <span class="text-nowrap"><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}</span>
                            <x-slot:end>
                                <div class="text-end">
                                    @can('view', $eventSeriesItem->organization)
                                        <a href="{{ $eventSeriesItem->organization->getRoute() }}">{{ $eventSeriesItem->organization->name }}</a>
                                    @else
                                        {{ $eventSeriesItem->organization->name }}
                                    @endif
                                </div>
                            </x-slot:end>
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
                                    @can('view', $eventSeriesItem)
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
                        @can('viewResponsibilities', $eventSeriesItem)
                            <x-bs::list.item>
                                <span class="text-nowrap"><i class="fa fa-fw fa-list-check"></i> {{ __('Responsibilities') }}</span>
                                <x-slot:end>
                                    @include('users.shared.responsible_user_span', [
                                        'class' => 'text-end ms-2',
                                        'users' => $eventSeriesItem->getResponsibleUsersVisibleForCurrentUser(),
                                    ])
                                </x-slot:end>
                            </x-bs::list.item>
                        @endcan
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
                    @canany(['update', 'createChild'], $eventSeriesItem)
                        <div class="card-body d-flex flex-wrap gap-1 d-print-none">
                            @can('update', $eventSeriesItem)
                                <x-button.edit href="{{ route('event-series.edit', $eventSeriesItem) }}"/>
                            @endcan
                            @can('createChild', $eventSeriesItem)
                                <x-bs::button.link href="{{ route('event-series.create', ['parent_event_series_id' => $eventSeriesItem->id, 'organization_id' => $eventSeriesItem->organization->id]) }}">
                                    <i class="fa fa-fw fa-plus"></i> {{ __('Create event series') }}
                                </x-bs::button.link>
                            @endcan
                            @can('forceDelete', $eventSeriesItem)
                                <x-form.delete-modal :id="$eventSeriesItem->id"
                                                     :name="$eventSeriesItem->name"
                                                     :route="route('event-series.destroy', $eventSeriesItem)"/>
                            @endcan
                        </div>
                    @endcanany
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$eventSeriesItem"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $eventSeries->withQueryString()->links() }}
@endsection
