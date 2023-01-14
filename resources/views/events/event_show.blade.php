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
        <span class="badge bg-primary">
            <span>
                <i class="fa fa-fw fa-calendar-week"></i>
                {{ __('Part of the event series') }}
            </span>
            <a class="link-light" href="{{ route('event-series.show', $event->eventSeries->slug) }}">{{ $event->eventSeries->name }}</a>
        </span>
    @endisset
    @isset($event->parentEvent)
        <span class="badge bg-primary">
            <span>
                <i class="fa fa-fw fa-calendar-days"></i>
                {{ __('Part of the event') }}
            </span>
            <a class="link-light" href="{{ route('events.show', $event->parentEvent) }}">{{ $event->parentEvent->name }}</a>
        </span>
    @endisset

    <div class="row my-3">
        <div class="col-12 col-md-4">
            <x-list.group>
                @isset($event->description)
                    <li class="list-group-item">
                        {{ $event->description }}
                    </li>
                @endisset
                @isset($event->website_url)
                    <li class="list-group-item">
                        <a href="{{ $event->website_url }}" target="_blank">{{ __('Website') }}</a>
                    </li>
                @endisset
                <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-eye" title="{{ __('Visibility') }}"></i>
                    </span>
                    <div><x-badge.visibility :visibility="$event->visibility"/></div>
                </li>
                <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-clock" title="{{ __('Date') }}"></i>
                    </span>
                    <div>@include('events.shared.event_dates')</div>
                </li>
                <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-location-pin" title="{{ __('Address') }}"></i>
                    </span>
                    <div>
                        @foreach($event->location->fullAddressBlock as $line)
                            {{ $line }}@if(!$loop->last)
                                <br>
                            @endif
                        @endforeach
                    </div>
                </li>
                <li class="list-group-item d-flex">
                    <span class="me-3">
                        <i class="fa fa-fw fa-sitemap" title="{{ __('Organizations') }}"></i>
                    </span>
                    <div>
                        @if($event->organizations->count() === 0)
                            {{ __('none') }}
                        @else
                            <ul class="list-unstyled">
                                @foreach($event->organizations as $organization)
                                    <li>{{ $organization->name }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </li>
            </x-list.group>
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
