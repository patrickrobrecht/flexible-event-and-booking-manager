@extends('layouts.app')

@php
    /** @var \App\Models\User $user */
    $user = \Illuminate\Support\Facades\Auth::user();
@endphp

@section('title')
    {{ __('Edit my account') }}
@endsection

@section('breadcrumbs')
    @can('viewAccount', \App\Models\User::class)
        <x-bs::breadcrumb.item href="{{ route('account.show') }}">{{ __('My account') }}</x-bs::breadcrumb.item>
    @endcan
@endsection

@section('headline-buttons')
    @can('viewAbilities', \App\Models\User::class)
        <x-bs::button.link variant="secondary" href="{{ route('account.show.abilities') }}"><i class="fa fa-fw fa-user-shield"></i> {{ __('Abilities') }}</x-bs::button.link>
    @endcan
@endsection

@section('content')
    <x-bs::form method="PUT" action="{{ route('account.update') }}">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <x-bs::form.field name="first_name" type="text" maxlength="255" :required="true"
                                          :value="$user->first_name ?? null">{{ __('First name') }}</x-bs::form.field>
                    </div>
                    <div class="col-12 col-md-6">
                        <x-bs::form.field name="last_name" type="text" maxlength="255" :required="true"
                                          :value="$user->last_name ?? null">{{ __('Last name') }}</x-bs::form.field>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 col-md-6">
                        <x-bs::form.field name="date_of_birth" type="date"
                                          :value="$user?->date_of_birth?->format('Y-m-d') ?? null"><i class="fa fa-fw fa-cake-candles"></i> {{ __('Date of birth') }}</x-bs::form.field>
                    </div>
                    <div class="col-12 col-md-6">
                        <x-bs::form.field name="phone" type="text"
                                          :value="$user->phone ?? null"><i class="fa fa-fw fa-phone"></i> {{ __('Phone number') }}</x-bs::form.field>
                    </div>
                </div>
                <x-bs::form.field name="email" type="email" maxlength="255" :required="true"
                                  :value="$user->email ?? null"><i class="fa fa-fw fa-at"></i> {{ __('E-mail') }}</x-bs::form.field>
                @isset($user->email_verified_at)
                    <x-bs::alert variant="primary">
                        {{ __('The e-mail address has been verified at :email_verified_at', [
                            'email_verified_at' => formatDateTime($user->email_verified_at),
                        ]) }}
                    </x-bs::alert>
                @else
                    <x-bs::alert variant="danger">
                        {{ __('The e-mail address has not been verified yet.') }}
                        <a class="alert-link" href="{{ route('verification.notice') }}">{{ __('Verify e-mail address') }}</a>
                    </x-bs::alert>
                @endisset
                <x-bs::form.field name="password" type="password" autocomplete="new-password">
                    <i class="fa fa-fw fa-lock"></i> {{ __('New password') }}
                    <x-slot:hint>{{ __('Leave empty to keep the current password.') }}</x-slot:hint>
                </x-bs::form.field>
                <x-bs::form.field name="password_confirmation" type="password"
                                  autocomplete="new-password"><i class="fa fa-fw fa-lock"></i> {{ __('Confirm new password') }}</x-bs::form.field>
                <x-bs::form.field name="current_password" type="password">
                    <i class="fa fa-fw fa-lock-open"></i> {{ __('Current password') }}
                    <x-slot:hint>{{ __('Please confirm your current password when changing your password or e-mail address.') }}</x-slot:hint>
                </x-bs::form.field>
            </div>
            <div class="col-12 col-lg-6">
                @include('_shared.address_fields_form', [
                    'address' => $user,
                ])
            </div>
        </div>
        <x-bs::button><i class="fa fa-fw fa-save"></i> {{ __('Save') }}</x-bs::button>
    </x-bs::form>
@endsection
