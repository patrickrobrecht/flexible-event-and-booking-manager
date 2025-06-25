@extends('layouts.app')

@php
    use Portavice\Bladestrap\Support\Options;

    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Location[] $locations */
@endphp

@section('title')
    {{ __('Locations') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    @can('create', \App\Models\Location::class)
        <x-bs::button.link href="{{ route('locations.create') }}" class="d-print-none">
            <i class="fa fa-fw fa-plus"></i> {{ __('Create location') }}
        </x-bs::button.link>
    @endcan

    <x-form.filter>
        <div class="row">
            <div class="col-12 col-sm-6 col-xl-3">
                <x-bs::form.field id="name" name="filter[name]" type="text"
                                  :from-query="true">{{ __('Name') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-bs::form.field id="address" name="filter[address]" type="text"
                                  :from-query="true"><i class="fa fa-fw fa-road"></i> {{ __('Address') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-bs::form.field id="event_id" name="filter[event_id]" type="select"
                                  :options="Options::fromArray(\App\Models\Event::filterOptions())"
                                  :from-query="true"><i class="fa fa-fw fa-calendar-days"></i> {{ __('Events') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-bs::form.field id="organization_id" name="filter[organization_id]" type="select"
                                  :options="Options::fromModels($organizations, 'name')->prependMany(\App\Models\Organization::filterOptions())"
                                  :cast="\App\Enums\FilterValue::castToIntIfNoValue()"
                                  :from-query="true"><i class="fa fa-fw fa-sitemap"></i> {{ __('Organization') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\Location::sortOptions()->getNamesWithLabels()"
                                  :from-query="true"><i class="fa fa-fw fa-sort"></i> {{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$locations->total()"/>

    <div class="row my-3">
        @foreach($locations as $location)
            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <div class="card avoid-break">
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
                        @isset($location->website_url)
                            <x-bs::list.item>
                                <i class="fa fa-fw fa-display"></i>
                                <a href="{{ $location->website_url }}" target="_blank">{{ __('Website') }}</a>
                            </x-bs::list.item>
                        @endisset
                        <x-bs::list.item>
                            <span>
                                <i class="fa fa-fw fa-calendar-days"></i>
                                <a href="{{ route('events.index', ['filter[location_id]' => $location->id]) }}" target="_blank">
                                    {{ __('Events') }}
                                </a>
                            </span>
                            <x-slot:end>
                                <x-bs::badge>{{ formatInt($location->events_count) }}</x-bs::badge>
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
                                <x-bs::badge>{{ formatInt($location->organizations_count) }}</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                    </x-bs::list>
                    @canany(['update', 'forceDelete'], $location)
                        <div class="card-body d-print-none">
                            @can('update', $location)
                                <x-button.edit href="{{ route('locations.edit', $location) }}"/>
                            @endcan
                            @can('forceDelete', $location)
                                <x-form.delete-modal :id="$location->id"
                                                 :name="$location->name"
                                                 :route="route('locations.destroy', $location)"/>
                            @endcan
                        </div>
                    @endcanany
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$location"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $locations->withQueryString()->links() }}
@endsection
