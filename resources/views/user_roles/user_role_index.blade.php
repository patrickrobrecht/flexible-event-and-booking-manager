@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\UserRole[] $userRoles */
@endphp

@section('title')
    {{ __('User roles') }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-button.group>
        @can('create', \App\Models\UserRole::class)
            <x-button.create href="{{ route('user-roles.create') }}">
                {{ __('Create user role') }}
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
                    <x-list.group class="list-group-flush">
                        <x-list.item>
                            <span>
                                <i class="fa fa-fw fa-users"></i>
                                <a href="{{ route('users.index', ['filter[user_role_id]' => $userRole->id]) }}" target="_blank">
                                    {{ __('Users') }}
                                </a>
                            </span>
                            <x-badge.counter>{{ formatInt($userRole->users_count) }}</x-badge.counter>
                        </x-list.item>
                    </x-list.group>
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
