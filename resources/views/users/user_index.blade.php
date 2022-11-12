@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\User[] $users */
@endphp

@section('title')
    {{ __('Users') }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-button.group>
        @can('create', \App\Models\User::class)
            <x-button.create href="{{ route('users.create') }}">
                {{ __('Create user') }}
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
                    <x-form.label for="email">{{ __('E-mail') }}</x-form.label>
                    <x-form.input id="email" name="filter[email]" />
                </x-form.row>
            </div>
            <div class="col-12 col-sm-6 col-lg">
                <x-form.row>
                    <x-form.label for="user_role_id">{{ __('User role') }}</x-form.label>
                    <x-form.select id="user_role_id" name="filter[user_role_id]"
                                   :options="$userRoles->pluck('name', 'id')" />
                </x-form.row>
            </div>
            <div class="col-12 col-sm-6 col-lg">
                <x-form.row>
                    <x-form.label for="status">{{ __('Status') }}</x-form.label>
                    <x-form.select id="status" name="filter[status]"
                                   :options="\App\Options\ActiveStatus::keysWithNamesAndAll()"
                                   :value="\App\Options\ActiveStatus::Active->value" />
                </x-form.row>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$users->total()" />

    <div class="row my-3">
        @foreach($users as $user)
            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ $user->name }}</h2>
                        <p class="card-subtitle text-muted">
                            {{ __('Last login') }}: {{ $user->last_login_at ? formatDateTime($user->last_login_at) : __('never') }}
                        </p>
                    </div>
                    <div class="card-body">
                        <div class="d-md-flex mb-3">
                            <span class="flex-grow-1">
                                @foreach($user->userRoles as $userRole)
                                    <span class="badge bg-primary">{{ $userRole->name }}</span>
                                @endforeach
                            </span>
                            <span>
                                <x-badge.active-status :active="$user->status" />
                            </span>
                        </div>

                        @can('update', $user)
                            <x-button.edit href="{{ route('users.edit', $user) }}"/>
                        @endcan
                    </div>
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$user" />
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $users->withQueryString()->links() }}
@endsection
