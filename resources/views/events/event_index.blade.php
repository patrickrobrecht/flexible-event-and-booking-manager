@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Event[] $events */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations */
@endphp

@section('title')
    {{ __('Events') }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-bs::button.group>
        @can('create', \App\Models\Event::class)
            <x-button.create href="{{ route('events.create') }}">
                {{ __('Create event') }}
            </x-button.create>
        @endcan
    </x-bs::button.group>

    <x-form.filter method="GET">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input id="name" name="filter[name]"/>
                </x-form.row>
            </div>
            <div class="col-12 col-md-6 col-xl">
                <x-form.row>
                    <x-form.label for="location_id">{{ __('Location') }}</x-form.label>
                    <x-form.select id="location_id" name="filter[location_id]"
                                   :options="$locations->pluck('nameOrAddress', 'id')">
                        <option value="">{{ __('all') }}</option>
                    </x-form.select>
                </x-form.row>
            </div>
            <div class="col-12 col-md-6 col-xl">
                <x-form.row>
                    <x-form.label for="organization_id">{{ __('Organization') }}</x-form.label>
                    <x-form.select id="organization_id" name="filter[organization_id]"
                                   :options="$organizations->pluck('name', 'id')">
                        <option value="">{{ __('all') }}</option>
                    </x-form.select>
                </x-form.row>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$events->total()"/>

    <div class="row my-3">
        @foreach($events as $event)
            <div class="col-12 col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <a href="{{ route('events.show', $event->slug) }}">{{ $event->name }}</a>
                        </h2>
                    </div>
                    <x-bs::list :flush="true">
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-eye" title="{{ __('Visibility') }}"></i>
                            <x-badge.visibility :visibility="$event->visibility"/>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-clock" title="{{ __('Date') }}"></i>
                            <span class="text-end">@include('events.shared.event_dates')</span>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-location-pin" title="{{ __('Location') }}"></i>
                            <span class="d-inline-block">
                                <div class="d-flex flex-column">
                                    @foreach($event->location->fullAddressBlock as $line)
                                        <div>{{ $line }}</div>
                                    @endforeach
                                </div>
                            </span>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span>
                                <i class="fa fa-fw fa-sitemap"></i>
                                {{ __('Organizations') }}
                            </span>
                            <x-slot:end>
                                <div class="text-end">
                                    <ul class="list-unstyled">
                                        @foreach($event->organizations as $organization)
                                            <li>{{ $organization->name }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </x-slot:end>
                        </x-bs::list.item>
                        @isset($event->eventSeries)
                            <x-bs::list.item>
                                <span>
                                    <i class="fa fa-fw fa-calendar-week"></i>
                                    {{ __('Part of the event series') }}
                                </span>
                                <x-slot:end>
                                    <span class="text-end">
                                        <a href="{{ route('event-series.show', $event->eventSeries->slug) }}" target="_blank">
                                            {{ $event->eventSeries->name }}
                                        </a>
                                    </span>
                                </x-slot:end>
                            </x-bs::list.item>
                        @endisset
                        @include('events.shared.event_booking_options')
                    </x-bs::list>
                    <div class="card-body">
                        @can('update', $event)
                            <x-button.edit href="{{ route('events.edit', $event) }}"/>
                        @endcan

                        @can('create', \App\Models\BookingOption::class)
                            <x-button.create href="{{ route('booking-options.create', $event) }}">
                                {{ __('Create booking option') }}
                            </x-button.create>
                        @endcan
                    </div>
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$event"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $events->withQueryString()->links() }}
@endsection
