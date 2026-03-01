@extends('layouts.app')

@php
    use App\Enums\FilterValue;
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
    @can('create', \App\Models\Organization::class)
        <x-bs::button.link href="{{ route('organizations.create') }}" class="d-print-none">
            <i class="fa fa-fw fa-plus"></i> {{ __('Create organization') }}
        </x-bs::button.link>
    @endcan

    <x-form.filter>
        <div class="row">
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="name" name="filter[name]" type="text"
                                  :from-query="true">{{ __('Name') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="event_id" name="filter[event_id]" type="select"
                                  :options="Options::fromArray(\App\Models\Event::filterOptions())"
                                  :from-query="true"><i class="fa fa-fw fa-calendar-days"></i> {{ __('Events') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="location_id" name="filter[location_id]" type="select"
                                  :options="Options::fromModels($locations, 'nameOrAddress')->prepend(__('all'), FilterValue::All->value)"
                                  :cast="FilterValue::castToIntIfNoValue()"
                                  :from-query="true"><i class="fa fa-fw fa-location-pin"></i> {{ __('Location') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="document_id" name="filter[document_id]" type="select"
                                  :options="Options::fromArray(\App\Models\Document::filterOptions())"
                                  :from-query="true"><i class="fa fa-fw fa-file"></i> {{ __('Documents') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-bs::form.field id="status" name="filter[status]" type="select"
                                  :options="\App\Enums\ActiveStatus::toOptionsWithAll()"
                                  :cast="FilterValue::castToIntIfNoValue()"
                                  :from-query="true"><i class="fa fa-fw fa-circle-question"></i> {{ __('Status') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\Organization::sortOptions()->getNamesWithLabels()"
                                  :from-query="true"><i class="fa fa-fw fa-sort"></i> {{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$organizations->total()"/>

    <div class="row my-3">
        @foreach($organizations as $organization)
            <div class="col-12 col-xl-6 mb-3">
                <div class="card avoid-break">
                    <div class="card-header">
                        <h2 class="card-title">
                            <a href="{{ $organization->getRoute() }}">{{ $organization->name }}</a>
                        </h2>
                        <x-badge.enum :case="$organization->status"/>
                    </div>
                    <x-bs::list :flush="true">
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
                        @isset($organization->register_entry)
                            <x-bs::list.item>
                                <span class="text-nowrap"><i class="fa fa-fw fa-scale-balanced"></i> {{ __('Register entry') }}</span>
                                <x-slot:end>
                                    <span class="text-end">{{ $organization->register_entry }}</span>
                                </x-slot:end>
                            </x-bs::list.item>
                        @endisset
                        @isset($organization->phone)
                            <x-bs::list.item>
                                <i class="fa fa-fw fa-phone"></i>
                                <a href="{{ $organization->phone_link }}">{{ $organization->phone }}</a>
                            </x-bs::list.item>
                        @endisset
                        @isset($organization->email)
                            <x-bs::list.item>
                                <i class="fa fa-fw fa-at"></i>
                                <a href="mailto:{{ $organization->email }}">{{ $organization->email }}</a>
                            </x-bs::list.item>
                        @endisset
                        @isset($organization->website_url)
                            <x-bs::list.item>
                                <i class="fa fa-fw fa-display"></i>
                                <a href="{{ $organization->website_url }}" target="_blank">{{ __('Website') }}</a>
                            </x-bs::list.item>
                        @endisset
                        @can('viewResponsibilities', $organization)
                            <x-bs::list.item>
                                <span class="text-nowrap"><i class="fa fa-fw fa-list-check"></i> {{ __('Responsibilities') }}</span>
                                <x-slot:end>
                                    @include('users.shared.responsible_user_span', [
                                        'class' => 'text-end ms-2',
                                        'users' => $organization->getResponsibleUsersVisibleForCurrentUser(),
                                    ])
                                </x-slot:end>
                            </x-bs::list.item>
                        @endcan
                        <x-bs::list.item>
                            <span class="text-nowrap">
                                <i class="fa fa-fw fa-file"></i>
                                @can('viewAny', [\App\Models\Document::class, $organization])
                                    <a href="{{ route('organizations.show', $organization) }}#documents">{{ __('Documents') }}</a>
                                @else
                                    {{ __('Documents') }}
                                @endcan
                            </span>
                            <x-slot:end>
                                <x-bs::badge>{{ formatInt($organization->documents_count) }}</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span>
                                <i class="fa fa-fw fa-toolbox"></i>
                                @can('viewAny', \App\Models\Material::class)
                                    <a href="{{ route('materials.index', ['filter[organization_id]' => $organization->id]) }}" target="_blank">{{ __('Materials') }}</a>
                                @else
                                    {{ __('Materials') }}
                                @endcan
                            </span>
                            <x-slot:end>
                                <x-bs::badge>{{ formatInt($organization->materials_count) }}</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span>
                                <i class="fa fa-fw fa-calendar-days"></i>
                                @can('viewAny', \App\Models\Event::class)
                                    <a href="{{ route('events.index', [
                                        'filter[organization_id]' => $organization->id,
                                        'filter[date_from]' => '',
                                        'filter[event_type]' => '',
                                    ]) }}" target="_blank">{{ __('Events') }}</a>
                                @else
                                    {{ __('Events') }}
                                @endcan
                            </span>
                            <x-slot:end>
                                <x-bs::badge>{{ formatInt($organization->events_count) }}</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span>
                                <i class="fa fa-fw fa-calendar-week"></i>
                                @can('viewAny', \App\Models\EventSeries::class)
                                    <a href="{{ route('event-series.index', ['filter[organization_id]' => $organization->id]) }}" target="_blank">{{ __('Event series') }}</a>
                                @else
                                    {{ __('Event series') }}
                                @endcan
                            </span>
                            <x-slot:end>
                                <x-bs::badge>{{ formatInt($organization->event_series_count) }}</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                    </x-bs::list>
                    @canany(['update', 'forceDelete'], $organization)
                        <div class="card-body d-flex flex-wrap gap-1 d-print-none">
                            @can('update', $organization)
                                <x-button.edit href="{{ route('organizations.edit', $organization) }}"/>
                            @endcan
                            @can('forceDelete', $organization)
                                <x-form.delete-modal :id="$organization->id"
                                                     :name="$organization->name"
                                                     :route="route('organizations.destroy', $organization)"/>
                            @endcan
                        </div>
                    @endcanany
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$organization"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $organizations->withQueryString()->links() }}
@endsection
