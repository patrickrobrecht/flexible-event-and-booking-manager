@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Location[] $locations */
@endphp

@section('title')
    {{ __('Locations') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    <x-bs::button.group>
        @can('create', \App\Models\Location::class)
            <x-button.create href="{{ route('locations.create') }}">
                {{ __('Create location') }}
            </x-button.create>
        @endcan
    </x-bs::button.group>

    <x-form.filter>
        <div class="row">
            <div class="col-12 col-sm-6 col-lg">
                <x-bs::form.field id="name" name="filter[name]" type="text"
                                  :from-query="true">{{ __('Name') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-lg">
                <x-bs::form.field id="address" name="filter[address]" type="text"
                                  :from-query="true">{{ __('Address') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\Location::sortOptions()->getNamesWithLabels()"
                                  :from-query="true">{{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$locations->total()" />

    <div class="row my-3">
        @foreach($locations as $location)
            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ $location->nameOrAddress }}</h2>
                    </div>
                    <x-bs::list :flush="true">
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-road"></i>
                            <span class="d-inline-block">
                                <div class="d-flex flex-column">
                                    @foreach($location->addressBlock as $line)
                                        <div>{{ $line }}</div>
                                    @endforeach
                                </div>
                            </span>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span>
                                <i class="fa fa-fw fa-calendar-days"></i>
                                <a href="{{ route('events.index', ['filter[location_id]' => $location->id]) }}" target="_blank">
                                    {{ __('Events') }}
                                </a>
                            </span>
                            <x-slot:end>
                                <x-badge.counter>{{ formatInt($location->events_count) }}</x-badge.counter>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span>
                                <i class="fa fa-fw fa-sitemap"></i>
                                <a href="{{ route('organizations.index', ['filter[location_id]' => $location->id]) }}" target="_blank">
                                    {{ __('Organizations') }}
                                </a>
                            </span>
                            <x-slot:end>
                                <x-badge.counter>{{ formatInt($location->organizations_count) }}</x-badge.counter>
                            </x-slot:end>
                        </x-bs::list.item>
                    </x-bs::list>
                    <div class="card-body">
                        @can('update', $location)
                            <x-button.edit href="{{ route('locations.edit', $location) }}"/>
                        @endcan
                    </div>
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$location" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $locations->withQueryString()->links() }}
@endsection
