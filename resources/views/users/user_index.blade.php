@extends('layouts.app')

@php
    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\User[] $users */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\UserRole[] $userRoles */
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
                                   :options="$userRoles->pluck('name', 'id')">
                        <option value="">{{ __('all') }}</option>
                    </x-form.select>
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
            <div class="col-12 col-lg-6 col-xxl-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ $user->name }}</h2>
                    </div>
                    <x-list.group class="list-group-flush">
                        <x-list.item>
                            <span class="text-nowrap">
                                <i class="fa fa-fw fa-at"></i>
                                {{ __('E-mail') }}
                            </span>
                            <span class="text-end">
                                {{ $user->email }}
                                @isset($user->email_verified_at)
                                    <span class="badge bg-primary">{{ __('verified') }}</span>
                                @else
                                    <span class="badge bg-danger">{{ __('not verified') }}</span>
                                @endisset
                            </span>
                        </x-list.item>
                        @isset($user->phone)
                            <x-list.item>
                                <span class="text-nowrap">
                                    <i class="fa fa-fw fa-phone"></i>
                                    {{ __('Phone number') }}
                                </span>
                                <span class="text-end">{{ $user->phone }}</span>
                            </x-list.item>
                        @endisset
                        <x-list.item>
                            <span class="text-nowrap">
                                <i class="fa fa-fw fa-circle-question"></i>
                                {{ __('Status') }}
                            </span>
                            <span class="text-end">
                                <x-badge.active-status :active="$user->status" />
                            </span>
                        </x-list.item>
                        <x-list.item>
                            <span class="text-nowrap">
                                <i class="fa fa-fw fa-user-group"></i>
                                {{ __('User roles') }}
                            </span>
                            <span class="text-end">
                                @if($user->userRoles->count() === 0)
                                    {{ __('none') }}
                                @else
                                    @foreach($user->userRoles->sortBy('name') as $userRole)
                                        <span class="badge bg-primary">{{ $userRole->name }}</span>
                                    @endforeach
                                @endif
                            </span>
                        </x-list.item>
                        <x-list.item>
                            <span class="text-nowrap">
                                <i class="fa fa-fw fa-sign-in-alt"></i>
                                {{ __('Last login') }}
                            </span>
                            <span class="text-end">
                                {{ $user->last_login_at ? formatDateTime($user->last_login_at) : __('never') }}
                            </span>
                        </x-list.item>
                        <x-list.item>
                            <span class="text-nowrap">
                                <i class="fa fa-fw fa-file-contract"></i>
                                {{ __('Bookings') }}
                            </span>
                            <x-badge.counter>{{ formatInt($user->bookings_count) }}</x-badge.counter>
                        </x-list.item>
                    </x-list.group>
                    <div class="card-body">
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
