@extends('layouts.app')

@php
    use Portavice\Bladestrap\Support\Options;

    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\UserRole[] $userRoles */
@endphp

@section('title')
    {{ __('User roles') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    <x-bs::button.group>
        @can('create', \App\Models\UserRole::class)
            <x-button.create href="{{ route('user-roles.create') }}">
                {{ __('Create user role') }}
            </x-button.create>
        @endcan
    </x-bs::button.group>

    <x-form.filter>
        <div class="row">
            <div class="col-12 col-xl-6">
                <x-bs::form.field id="name" name="filter[name]" type="text"
                                  :from-query="true">{{ __('Name') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-md-6 col-xl-3">
                <x-bs::form.field id="user_id" name="filter[user_id]" type="select"
                                  :options="Options::fromArray(\App\Models\User::filterOptions())"
                                  :from-query="true"><i class="fa fa-fw fa-users"></i> {{ __('Users') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\UserRole::sortOptions()->getNamesWithLabels()"
                                  :from-query="true"><i class="fa fa-fw fa-sort"></i> {{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$userRoles->total()"/>

    <div class="row my-3">
        @foreach($userRoles as $userRole)
            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            @can('view', $userRole)
                                <a href="{{ route('user-roles.show', $userRole) }}">{{ $userRole->name }}</a>
                            @else
                                {{ $userRole->name }}
                            @endcan
                        </h2>
                    </div>
                    <x-bs::list :flush="true">
                        <x-bs::list.item>
                            <span>
                                <i class="fa fa-fw fa-users"></i>
                                <a href="{{ route('users.index', ['filter[user_role_id]' => $userRole->id]) }}" target="_blank">{{ __('Users') }}</a>
                            </span>
                            <x-slot:end>
                                <x-bs::badge>{{ formatInt($userRole->users_count) }}</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span><i class="fa fa-fw fa-user-shield"></i> {{ __('Abilities') }}</span>
                            <x-slot:end>
                                <x-bs::badge>{{ formatInt(count($userRole->abilities)) }}</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                    </x-bs::list>
                    @can('update', $userRole)
                        <div class="card-body">
                            <x-button.edit href="{{ route('user-roles.edit', $userRole) }}"/>
                        </div>
                    @endcan
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$userRole"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $userRoles->withQueryString()->links() }}
@endsection
