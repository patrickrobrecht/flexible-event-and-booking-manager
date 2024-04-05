@extends('layouts.app')

@php
    use Portavice\Bladestrap\Support\Options;

    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\Organization[] $organizations */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\Location[] $locations */
@endphp

@section('title')
    {{ __('Organizations') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    <x-bs::button.group>
        @can('create', \App\Models\Organization::class)
            <x-button.create href="{{ route('organizations.create') }}">
                {{ __('Create organization') }}
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
                <x-bs::form.field id="location_id" name="filter[location_id]" type="select"
                                  :options="Options::fromModels($locations, 'nameOrAddress')->prepend(__('all'), '')"
                                  :from-query="true">{{ __('Location') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\Organization::sortOptions()->getNamesWithLabels()"
                                  :from-query="true">{{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$organizations->total()"/>

    <div class="row my-3">
        @foreach($organizations as $organization)
            <div class="col-12 col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            <a href="{{ route('organizations.show', $organization) }}">{{ $organization->name }}</a>
                        </h2>
                        <x-badge.active-status :active="$organization->status" />
                    </div>
                    <x-bs::list :flush="true">
                        <x-bs::list.item>
                            <span>
                                <i class="fa fa-fw fa-calendar-days"></i>
                                <a href="{{ route('events.index', ['filter[organization_id]' => $organization->id]) }}" target="_blank">{{ __('Events') }}</a>
                            </span>
                            <x-slot:end>
                                <x-badge.counter>{{ formatInt($organization->events_count) }}</x-badge.counter>
                            </x-slot:end>
                        </x-bs::list.item>
                        @isset($organization->website_url)
                            <x-bs::list.item>
                                <i class="fa fa-fw fa-display"></i>
                                <a href="{{ $organization->website_url }}" target="_blank">{{ __('Website') }}</a>
                            </x-bs::list.item>
                        @endisset
                        <x-bs::list.item>
                            <i class="fa fa-fw fa-location-pin"></i>
                            <span class="d-inline-block">
                                <div class="d-flex flex-column">
                                    @foreach($organization->location->fullAddressBlock as $line)
                                        <div>{{ $line }}</div>
                                    @endforeach
                                </div>
                            </span>
                        </x-bs::list.item>
                        @isset($organization->representatives)
                            <x-bs::list.item>
                                <span class="text-nowrap"><i class="fa fa-fw fa-user-friends"></i> {{ __('Representatives') }}</span>
                                <x-slot:end>
                                    <span class="text-end">{{ $organization->representatives }}</span>
                                </x-slot:end>
                            </x-bs::list.item>
                        @endisset
                        @isset($organization->register_entry)
                            <x-bs::list.item>
                                <span class="text-nowrap"><i class="fa fa-fw fa-scale-balanced"></i> {{ __('Register entry') }}</span>
                                <x-slot:end>
                                    <span class="text-end">{{ $organization->register_entry }}</span>
                                </x-slot:end>
                            </x-bs::list.item>
                        @endisset
                    </x-bs::list>
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
