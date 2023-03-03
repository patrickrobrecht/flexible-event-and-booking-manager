@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Organization[] $organizations */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations */
@endphp

@section('title')
    {{ __('Organizations') }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-button.group>
        @can('create', \App\Models\Organization::class)
            <x-button.create href="{{ route('organizations.create') }}">
                {{ __('Create organization') }}
            </x-button.create>
        @endcan
    </x-button.group>

    <x-form.filter method="GET">
        <div class="row">
            <div class="col-12 col-sm-6 col-lg">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input id="name" name="filter[name]"/>
                </x-form.row>
            </div>
            <div class="col-12 col-sm-6 col-lg">
                <x-form.label for="location_id">{{ __('Location') }}</x-form.label>
                <x-form.select id="location_id" name="filter[location_id]"
                               :options="$locations->pluck('nameOrAddress', 'id')">
                    <option value="">{{ __('all') }}</option>
                </x-form.select>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$organizations->total()"/>

    <div class="row my-3">
        @foreach($organizations as $organization)
            <div class="col-12 col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ $organization->name }}</h2>
                        <x-badge.active-status :active="$organization->status" />
                    </div>
                    <x-list.group class="list-group-flush">
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-calendar-days"></i>
                                <a href="{{ route('events.index', ['filter[organization_id]' => $organization->id]) }}" target="_blank">
                                    {{ __('Events') }}
                                </a>
                            </span>
                            <x-badge.counter>{{ formatInt($organization->events_count) }}</x-badge.counter>
                        </x-list.item>
                        <x-list.item :flex="false">
                            <i class="fa fa-fw fa-location-pin"></i>
                            <span class="d-inline-block">
                                <div class="d-flex flex-column">
                                    @foreach($organization->location->fullAddressBlock as $line)
                                        <div>{{ $line }}</div>
                                    @endforeach
                                </div>
                            </span>
                        </x-list.item>
                        @isset($organization->register_entry)
                            <x-list.item>
                                <span>
                                    <i class="fa fa-fw fa-scale-balanced"></i>
                                    {{ __('Register entry') }}
                                </span>
                                <span class="text-end">{{ $organization->register_entry }}</span>
                            </x-list.item>
                        @endisset
                        @isset($organization->representatives)
                            <x-list.item>
                                <span>
                                    <i class="fa fa-fw fa-user-friends"></i>
                                    {{ __('Representatives') }}
                                </span>
                                <span class="text-end">{{ $organization->representatives }}</span>
                            </x-list.item>
                        @endisset
                    </x-list.group>
                    <div class="card-body">
                        @can('update', $organization)
                            <x-button.edit href="{{ route('organizations.edit', $organization) }}"/>
                        @endcan
                    </div>
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$organization"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $organizations->withQueryString()->links() }}
@endsection
