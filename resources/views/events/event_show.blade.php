@extends('layouts.app')

@php
    /** @var \App\Models\Event $event */
@endphp

@section('title')
    {{ $event->name }}
@endsection

@section('breadcrumbs')
    @can('viewAny', \App\Models\Event::class)
        <x-bs::breadcrumb.item href="{{ route('events.index') }}">{{ __('Events') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('Events') }}</x-bs::breadcrumb.item>
    @endcan
    @isset($event->parentEvent)
        <x-bs::breadcrumb.item href="{{ route('events.show', $event->parentEvent) }}">{{ $event->parentEvent->name }}</x-bs::breadcrumb.item>
    @endisset
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('headline-buttons')
    @can('update', $event)
        <x-button.edit href="{{ route('events.edit', $event) }}"/>
    @endcan
    @can('viewGroups', $event)
        <x-bs::button.link href="{{ route('groups.index', $event) }}" variant="secondary">
            <i class="fa fa-fw fa-user-group"></i> {{ __('Groups') }}
            <x-bs::badge variant="danger">{{ formatInt($event->groups_count) }}</x-bs::badge>
        </x-bs::button.link>
    @endcan
@endsection

@section('content')
    @isset($event->eventSeries)
        <x-bs::badge variant="primary">
            <span>
                <i class="fa fa-fw fa-calendar-week"></i>
                {{ __('Part of the event series') }}
            </span>
            <a class="link-light" href="{{ route('event-series.show', $event->eventSeries->slug) }}">{{ $event->eventSeries->name }}</a>
        </x-bs::badge>
    @endisset
    @isset($event->parentEvent)
        <x-bs::badge variant="primary">
            <span>
                <i class="fa fa-fw fa-calendar-days"></i>
                {{ __('Part of the event') }}
            </span>
            <a class="link-light" href="{{ route('events.show', $event->parentEvent) }}">{{ $event->parentEvent->name }}</a>
        </x-bs::badge>
    @endisset

    <div class="row my-3">
        <div class="col-12 col-md-4">
            @include('events.shared.event_details')
        </div>
        <div class="col-12 col-md-8">
            @php
                $bookingOptionsToShow = $event->bookingOptions->isNotEmpty()
                    || Auth::user()?->can('create', [\App\Models\BookingOption::class, $event])
            @endphp
            @if($bookingOptionsToShow)
                @if($event->bookingOptions->count() > 0)
                    <x-bs::list>
                        @include('events.shared.event_booking_options')
                    </x-bs::list>
                @endif
                @can('create', [\App\Models\BookingOption::class, $event])
                    <div @class([
                        'mt-3' => $event->bookingOptions->isNotEmpty(),
                    ])>
                        <x-button.create href="{{ route('booking-options.create', $event) }}">
                            {{ __('Create booking option') }}
                        </x-button.create>
                    </div>
                @endcan
            @endif

            @php
                $eventsSectionEmpty = true;
            @endphp
            @isset($event->parentEvent)
                @php
                    $siblingEvents = $event->parentEvent->subEvents->keyBy('id')->except($event->id);
                @endphp
                @if($siblingEvents->isNotEmpty())
                    @php
                        $eventsSectionEmpty = false;
                    @endphp
                    <section @class([
                        'mt-4' => $bookingOptionsToShow,
                    ])>
                        <h2>{{ __('Other sub events of :name', [
                            'name' => $event->parentEvent->name,
                        ]) }}</h2>
                        @include('events.shared.event_list', [
                            'events' => $siblingEvents,
                        ])
                    </section>
                @endif
            @elseif($event->subEvents->isNotEmpty() || Auth::user()?->can('createChild', $event))
                @php
                    $eventsSectionEmpty = false;
                @endphp
                <section @class([
                    'mt-4' => $bookingOptionsToShow,
                ])>
                    <h2>{{ __('Sub events') }}</h2>
                    @if($event->subEvents->isNotEmpty())
                        @include('events.shared.event_list', [
                            'events' => $event->subEvents,
                        ])
                    @endif
                    @can('createChild', $event)
                        <div class="mt-3">
                            <x-button.create href="{{ route('events.create', ['parent_event_id' => $event->id]) }}">
                                {{ __('Create event') }}
                            </x-button.create>
                        </div>
                    @endcan
                </section>
            @endif

            @canany(['viewAny', 'create'], [\App\Models\Document::class, $event])
                <section @class([
                    'mt-4' => $bookingOptionsToShow || !$eventsSectionEmpty,
                ])>
                    <h2>{{ __('Documents') }}</h2>
                @can('viewAny', [\App\Models\Document::class, $event])
                    @include('documents.shared.document_list', [
                        'documents' => $event->documents,
                    ])
                @endcan
                @include('documents.shared.document_add_modal', [
                    'reference' => $event,
                    'routeForAddDocument' => route('events.documents.store', $event),
                ])
            @endcanany
        </div>
    </div>

    @can('update', $event)
        <x-text.updated-human-diff :model="$event"/>
    @endcan
@endsection
