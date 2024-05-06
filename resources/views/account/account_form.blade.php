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
        <x-bs::button.link variant="secondary" href="{{ route('account.show.abilities') }}">{{ __('View abilities') }}</x-bs::button.link>
    @endcan
@endsection

@section('content')
    <x-bs::form method="PUT" action="{{ route('account.update') }}">
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="row">
                    <div class="col-12 col-md-6">
                        <x-bs::form.field name="first_name" type="text"
                                          :value="$user->first_name ?? null">{{ __('First name') }}</x-bs::form.field>
                    </div>
                    <div class="col-12 col-md-6">
                        <x-bs::form.field name="last_name" type="text"
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
                <x-bs::form.field name="email" type="email"
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
                    <i class="fa fa-fw fa-key"></i> {{ __('New password') }}
                    <x-slot:hint>{{ __('Leave empty to keep the current password.') }}</x-slot:hint>
                </x-bs::form.field>
                <x-bs::form.field name="password_confirmation" type="password"
                                  autocomplete="new-password"><i class="fa fa-fw fa-key"></i> {{ __('Confirm password') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-6">
                @include('_shared.address_fields_form', [
                    'address' => $user,
                ])
            </div>
        </div>

        <x-button.save>
            @isset($user){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
        </x-button.save>
    </x-bs::form>
@endsection
