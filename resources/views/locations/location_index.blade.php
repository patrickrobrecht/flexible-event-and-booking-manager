@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Location[] $locations */
@endphp

@section('title')
    {{ __('Locations') }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-button.group>
        @can('create', \App\Models\Location::class)
            <x-button.create href="{{ route('locations.create') }}">
                {{ __('Create location') }}
            </x-button.create>
        @endcan
    </x-button.group>

    <x-form.filter method="GET">
        <div class="row">
            <div class="col-12 col-sm-6 col-lg">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input id="name" name="filter[name]" />
                </x-form.row>
            </div>
            <div class="col-12 col-sm-6 col-lg">
                <x-form.row>
                    <x-form.label for="address">{{ __('Address') }}</x-form.label>
                    <x-form.input id="address" name="filter[address]" />
                </x-form.row>
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
                    <x-list.group class="list-group-flush">
                        <x-list.item :flex="false">
                            <i class="fa fa-fw fa-road"></i>
                            <span class="d-inline-block">
                                <div class="d-flex flex-column">
                                    @foreach($location->addressBlock as $line)
                                        <div>{{ $line }}</div>
                                    @endforeach
                                </div>
                            </span>
                        </x-list.item>
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-calendar-days"></i>
                                <a href="{{ route('events.index', ['filter[location_id]' => $location->id]) }}" target="_blank">
                                    {{ __('Events') }}
                                </a>
                            </span>
                            <x-badge.counter>{{ formatInt($location->events_count) }}</x-badge.counter>
                        </x-list.item>
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-sitemap"></i>
                                <a href="{{ route('organizations.index', ['filter[location_id]' => $location->id]) }}" target="_blank">
                                    {{ __('Organizations') }}
                                </a>
                            </span>
                            <x-badge.counter>{{ formatInt($location->organizations_count) }}</x-badge.counter>
                        </x-list.item>
                    </x-list.group>
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
