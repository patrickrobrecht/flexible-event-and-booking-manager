@extends('layouts.app')

@php
    use Portavice\Bladestrap\Support\Options;

    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Event[] $events */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations */
@endphp

@section('title')
    {{ __('Events') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    <x-bs::button.group>
        @can('create', \App\Models\Event::class)
            <x-button.create href="{{ route('events.create') }}">
                {{ __('Create event') }}
            </x-button.create>
        @endcan
    </x-bs::button.group>

    <x-form.filter>
        <div class="row">
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="name" name="filter[name]" type="text"
                                  :from-query="true">{{ __('Name') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="visibility" name="filter[visibility]" type="select"
                                  :options="\App\Options\Visibility::toOptionsWithAll()"
                                  :from-query="true">{{ __('Visibility') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="date_from" name="filter[date_from]" type="date"
                                  :from-query="true">{{ __('Start of the period') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="date_until" name="filter[date_until]" type="date"
                                  :from-query="true">{{ __('End of the period') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="location_id" name="filter[location_id]" type="select"
                                  :options="Options::fromModels($locations, 'nameOrAddress')->prepend(__('all'), '')"
                                  :from-query="true">{{ __('Location') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="organization_id" name="filter[organization_id]" type="select"
                                  :options="Options::fromModels($organizations, 'name')->prepend(__('all'), '')"
                                  :from-query="true">{{ __('Organization') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="event_type" name="filter[event_type]" type="select"
                                  :options="\App\Options\EventType::toOptionsWithAll()"
                                  :from-query="true">{{ __('Event type') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\Event::sortOptions()->getNamesWithLabels()"
                                  :from-query="true">{{ __('Sorting') }}</x-bs::form.field>
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
                        @can('createChild', $event)
                            <x-button.create href="{{ route('events.create', ['parent_event_id' => $event->id]) }}">
                                {{ __('Create event') }}
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
