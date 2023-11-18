@extends('layouts.app')

@php
    /** @var \App\Models\Event $event */
@endphp

@section('title')
    {{ $event->name }}
@endsection

@section('breadcrumbs')
    @can('viewAny', \App\Models\Event::class)
        <x-nav.breadcrumb href="{{ route('events.index') }}">{{ __('Events') }}</x-nav.breadcrumb>
    @else
        <x-nav.breadcrumb>{{ __('Events') }}</x-nav.breadcrumb>
    @endcan
    <x-nav.breadcrumb/>
@endsection

@section('headline-buttons')
    @can('update', $event)
        <x-button.edit href="{{ route('events.edit', $event) }}"/>
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
            <x-list.group class="mb-3">
                @include('events.shared.event_booking_options')
            </x-list.group>

            @can('create', \App\Models\BookingOption::class)
                <div class="mb-3">
                    <x-button.create href="{{ route('booking-options.create', $event) }}">
                        {{ __('Create booking option') }}
                    </x-button.create>
                </div>
            @endcan

            @if($event->subEvents->count() > 0 || Auth::user()?->can('createChild', $event))
                @include('events.shared.event_list', [
                    'events' => $event->subEvents,
                ])

                @can('createChild', $event)
                    <div class="mt-3">
                        <x-button.create href="{{ route('events.create', ['parent_event_id' => $event->id]) }}">
                            {{ __('Create event') }}
                        </x-button.create>
                    </div>
                @endcan
            @endif
        </div>
    </div>

    <x-text.updated-human-diff :model="$event"/>
@endsection
