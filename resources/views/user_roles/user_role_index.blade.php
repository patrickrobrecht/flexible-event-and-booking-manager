@extends('layouts.app')

@php
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

    <x-form.filter method="GET">
        <div class="row">
            <div class="col-12 col-sm-6 col-lg">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input id="name" name="filter[name]" />
                </x-form.row>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$userRoles->total()" />

    <div class="row my-3">
        @foreach($userRoles as $userRole)
            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ $userRole->name }}</h2>
                    </div>
                    <x-bs::list :flush="true">
                        <x-bs::list.item>
                            <span>
                                <i class="fa fa-fw fa-users"></i>
                                <a href="{{ route('users.index', ['filter[user_role_id]' => $userRole->id]) }}" target="_blank">
                                    {{ __('Users') }}
                                </a>
                            </span>
                            <x-slot:end>
                                <x-badge.counter>{{ formatInt($userRole->users_count) }}</x-badge.counter>
                            </x-slot:end>
                        </x-bs::list.item>
                    </x-bs::list>
                    <div class="card-body">
                        @can('update', $userRole)
                            <x-button.edit href="{{ route('user-roles.edit', $userRole) }}"/>
                        @endcan
                    </div>
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$userRole" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $userRoles->withQueryString()->links() }}
@endsection
