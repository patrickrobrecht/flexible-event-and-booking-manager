@extends('layouts.app')

@php
    /** @var ?\App\Models\User $editedUser */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\UserRole[] $userRoles */
@endphp

@section('title')
    @isset($editedUser)
        {{ __('Edit :name', ['name' => $editedUser->name]) }}
    @else
        {{ __('Create user') }}
    @endisset
@endsection

@section('breadcrumbs')
    @can('viewAny', \App\Models\User::class)
        <x-bs::breadcrumb.item href="{{ route('users.index') }}">{{ __('Users') }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ __('Users') }}</x-bs::breadcrumb.item>
    @endcan
    @isset($editedUser)
        @can('view', $editedUser)
            <x-bs::breadcrumb.item href="{{ route('users.show', $editedUser) }}">{{ $editedUser->name }}</x-bs::breadcrumb.item>
        @else
            <x-bs::breadcrumb.item>{{ $editedUser->name }}</x-bs::breadcrumb.item>
        @endcan
    @endisset
@endsection

@section('content')
    <x-bs::form method="{{ isset($editedUser) ? 'PUT' : 'POST' }}"
                action="{{ isset($editedUser) ? route('users.update', $editedUser) : route('users.store') }}">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <x-bs::form.field name="first_name" type="text"
                                          :value="$editedUser->first_name ?? null">{{ __('First name') }}</x-bs::form.field>
                    </div>
                    <div class="col-12 col-md-6">
                        <x-bs::form.field name="last_name" type="text"
                                          :value="$editedUser->last_name ?? null">{{ __('Last name') }}</x-bs::form.field>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <x-bs::form.field name="date_of_birth" type="date"
                                          :value="($editedUser ?? null)?->date_of_birth?->format('Y-m-d') ?? null"><i class="fa fa-fw fa-cake-candles"></i> {{ __('Date of birth') }}</x-bs::form.field>
                    </div>
                    <div class="col-12 col-md-6">
                        <x-bs::form.field name="phone" type="tel"
                                          :value="$editedUser->phone ?? null"><i class="fa fa-fw fa-phone"></i> {{ __('Phone number') }}</x-bs::form.field>
                    </div>
                </div>
                <x-bs::form.field name="email" type="email"
                                  :value="$editedUser->email ?? null"><i class="fa fa-fw fa-at"></i> {{ __('E-mail') }}</x-bs::form.field>
                @isset($editedUser->email_verified_at)
                    <x-bs::alert variant="primary">
                        {{ __('The e-mail address has been verified at :email_verified_at', [
                            'email_verified_at' => formatDateTime($editedUser->email_verified_at),
                        ]) }}
                    </x-bs::alert>
                @else
                    <x-bs::alert variant="danger">{{ __('The e-mail address has not been verified yet.') }}</x-bs::alert>
                @endisset
                <x-bs::form.field name="password" type="password" autocomplete="new-password">
                    <i class="fa fa-fw fa-key"></i> {{ __('New password') }}
                    <x-slot:hint>
                        @isset($editedUser->password)
                            {{ __('Leave empty to keep the current password.') }}
                        @else
                            <span class="fw-bold text-danger">{{ __('No password is currently set for this user.') }}</span>
                        @endisset
                    </x-slot:hint>
                </x-bs::form.field>
                <x-bs::form.field name="password_confirmation" type="password"
                                  autocomplete="new-password"><i class="fa fa-fw fa-key"></i> {{ __('Confirm password') }}</x-bs::form.field>
                <x-bs::form.field id="user_role_id" name="user_role_id[]" type="switch"
                                  :options="$userRoles->pluck('name', 'id')"
                                  :value="isset($editedUser) ? $editedUser->userRoles->pluck('id')->toArray() : []"
                                  :valuesToInt="true"><i class="fa fa-fw fa-user-group"></i> {{ __('User role') }}</x-bs::form.field>
                <x-bs::form.field name="status" type="select"
                                  :options="\App\Options\ActiveStatus::toOptions()"
                                  :value="$editedUser->status->value ?? null"><i class="fa fa-fw fa-circle-question"></i> {{ __('Status') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-6">
                @include('_shared.address_fields_form', [
                    'address' => $editedUser ?? null,
                ])
            </div>
        </div>

        <x-bs::button.group>
            <x-button.save>
                @isset($editedUser){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
            </x-button.save>
            <x-button.cancel href="{{ route('users.index') }}"/>
        </x-bs::button.group>
    </x-bs::form>

    <x-text.timestamp :model="$editedUser ?? null" />
@endsection
