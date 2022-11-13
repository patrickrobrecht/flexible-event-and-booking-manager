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
    <x-button.group>
        @can('create', \App\Models\Event::class)
            <x-button.create href="{{ route('events.create') }}">
                {{ __('Create event') }}
            </x-button.create>
        @endcan
    </x-button.group>

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
                            <a href="{{ route('events.show', $event->slug) }}" target="_blank">
                                {{ $event->name }}
                            </a>
                        </h2>
                    </div>
                    <x-list.group class="list-group-flush">
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-clock"></i>
                                {{ __('Date') }}
                            </span>
                            <span class="text-end">
                                {{ __(':start until :end', [
                                    'start' => isset($event->started_at) ? formatDateTime($event->started_at) : '?',
                                    'end' => isset($event->finished_at) ? formatDateTime($event->finished_at) : '?',
                                ]) }}
                            </span>
                        </x-list.item>
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-location-pin"></i>
                                {{ __('Address') }}
                            </span>
                            <span class="text-end">
                                @foreach($event->location->fullAddressBlock as $line)
                                    {{ $line }}@if(!$loop->last)
                                        <br>
                                    @endif
                                @endforeach
                            </span>
                        </x-list.item>
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-sitemap"></i>
                                {{ __('Organizations') }}
                            </span>
                            <span class="text-end">
                                <ul class="list-unstyled">
                                    @foreach($event->organizations as $organization)
                                        <li>{{ $organization->name }}</li>
                                    @endforeach
                                </ul>
                            </span>
                        </x-list.item>
                    </x-list.group>
                    <div class="card-body">
                        @can('update', $event)
                            <x-button.edit href="{{ route('events.edit', $event) }}"/>
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
