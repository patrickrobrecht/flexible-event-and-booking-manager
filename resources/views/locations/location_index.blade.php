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
                        <h2 class="card-title">
                            <a href="{{ $location->getRoute() }}">{{ $location->nameOrAddress }}</a>
                        </h2>
                    </div>
                    @include('locations.shared.location_details', [
                        'flush' => true,
                    ])
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
