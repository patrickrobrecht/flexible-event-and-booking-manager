@extends('layouts.app')

@php
    /** @var \App\Models\User $user */
    $user = \Illuminate\Support\Facades\Auth::user();
@endphp

@section('title')
    {{ __('My account') }}
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
                                          :value="$user?->date_of_birth?->format('Y-m-d') ?? null">{{ __('Date of birth') }}</x-bs::form.field>
                    </div>
                    <div class="col-12 col-md-6">
                        <x-bs::form.field name="phone" type="text"
                                          :value="$user->phone ?? null">{{ __('Phone number') }}</x-bs::form.field>
                    </div>
                </div>
                <x-bs::form.field name="email" type="email"
                                  :value="$user->email ?? null">{{ __('E-mail') }}</x-bs::form.field>
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
                    {{ __('New password') }}
                    <x-slot:hint>{{ __('Leave empty to keep the current password.') }}</x-slot:hint>
                </x-bs::form.field>
                <x-bs::form.field name="password_confirmation" type="password"
                                  autocomplete="new-password">{{ __('Confirm password') }}</x-bs::form.field>
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

    <section class="mt-3">
        <h2>{{ __('Abilities') }}</h2>
        <div class="mb-3">
            @foreach($user->userRoles as $userRole)
                <x-bs::badge variant="primary">{{ $userRole->name }}</x-bs::badge>
            @endforeach
        </div>

        {{ __('In :app you have the following abilities:', ['app' => config('app.name')]) }}
        <ul>
            @foreach($user->getAbilities() as $ability)
                <li>{{ $ability->getTranslatedName() }}</li>
            @endforeach
        </ul>
    </section>
@endsection
