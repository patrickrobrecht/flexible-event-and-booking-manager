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
        @can('viewAny', $eventSeries->parentEventSeries)
            <x-bs::breadcrumb.item href="{{ route('event-series.show', $eventSeries->parentEventSeries) }}">{{ $eventSeries->parentEventSeries->name }}</x-bs::breadcrumb.item>
        @else
            <x-bs::breadcrumb.item>{{ $eventSeries->parentEventSeries->name }}</x-bs::breadcrumb.item>
        @endcan
    @endisset
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $eventSeries)
        <x-button.edit href="{{ route('event-series.edit', $eventSeries) }}"/>
    @endcan
    @can('forceDelete', $eventSeries)
        <x-form.delete-modal :id="$eventSeries->id"
                             :name="$eventSeries->name"
                             :route="route('event-series.destroy', $eventSeries)"/>
    @endcan
@endsection

@section('content')
    @include('event_series.shared.event_series_badges')

    <div class="row my-3">
        <div id="events" class="col-12 col-xl-6 col-xxl-4">
            <h2><i class="fa fa-fw fa-calendar-days"></i> {{ __('Events') }}</h2>
            @include('event_series.shared.events_in_series')
            @can('create', \App\Models\Event::class)
                <div class="my-3">
                    <x-bs::button.link href="{{ route('events.create', ['event_series_id' => $eventSeries->id, 'organization_id' => $eventSeries->organization->id]) }}">
                        <i class="fa fa-fw fa-plus"></i> {{ __('Create event') }}
                    </x-bs::button.link>
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
                <h2><i class="fa fa-fw fa-calendar-week"></i> {{ __('Event series') }}</h2>
                @include('event_series.shared.event_series_list', [
                    'eventSeries' => $subEventSeriesList,
                    'showParentEventSeries' => false,
                ])
                @can('createChild', $eventSeries)
                    <div class="mt-3 d-print-none">
                        <x-bs::button.link href="{{ route('event-series.create', ['parent_event_series_id' => $eventSeries->id, 'organization_id' => $eventSeries->organization->id]) }}">
                            <i class="fa fa-fw fa-plus"></i> {{ __('Create event series') }}
                        </x-bs::button.link>
                    </div>
                @endcan
            </div>
        @endif
        <div @class([
            'col-12 col-xl-6 col-xxl-4',
            'mt-4 mt-xxl-0' => $hasSubEventSeriesToShow,
        ])>
            @php
                $responsibilitySectionEmpty = true;
            @endphp
            @can('viewResponsibilities', $eventSeries)
                @php
                    $responsibilitySectionEmpty = false;
                @endphp
                <section id="responsibilities">
                    <h2><i class="fa fa-fw fa-list-check"></i> {{ __('Responsibilities') }}</h2>
                    @include('users.shared.responsible_user_list', [
                        'users' => $eventSeries->getResponsibleUsersVisibleForCurrentUser(),
                    ])
                </section>
            @endcan
            @canany(['viewAny', 'create'], [\App\Models\Document::class, $eventSeries])
                <section id="documents" @class([
                    'mt-4' => !$responsibilitySectionEmpty,
                ])>
                    <h2><i class="fa fa-fw fa-file"></i> {{ __('Documents') }}</h2>
                    @can('viewAny', [\App\Models\Document::class, $eventSeries])
                        @include('documents.shared.document_list', [
                            'documents' => $eventSeries->documents,
                        ])
                    @endcan
                    @include('documents.shared.document_add_modal', [
                        'reference' => $eventSeries,
                        'routeForAddDocument' => route('event-series.documents.store', $eventSeries),
                    ])
                </section>
            @endcanany
       </div>
    </div>

    @can('update', $eventSeries)
        <x-text.updated-human-diff :model="$eventSeries"/>
    @endcan
@endsection
