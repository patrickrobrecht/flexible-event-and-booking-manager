@extends('layouts.app')

@php
    /** @var \App\Models\User $user */
    $user = \Illuminate\Support\Facades\Auth::user();
@endphp

@section('title')
    {{ __('My account') }}
@endsection

@section('content')
    <x-form method="PUT" action="{{ route('account.update') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="first_name">{{ __('First name') }}</x-form.label>
                    <x-form.input name="first_name" type="text"
                                  :value="$user->first_name ?? null" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="last_name">{{ __('Last name') }}</x-form.label>
                    <x-form.input name="last_name" type="text"
                                  :value="$user->last_name ?? null" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="email">{{ __('E-mail') }}</x-form.label>
                    <x-form.input name="email" type="email"
                                  :value="$user->email ?? null" />
                </x-form.row>
                @isset($user->email_verified_at)
                    <p class="alert alert-primary">
                        {{ __('The e-mail address has been verified at :email_verified_at', [
                            'email_verified_at' => formatDateTime($user->email_verified_at),
                        ]) }}
                    </p>
                @else
                    <p class="alert alert-danger">
                        {{ __('The e-mail address has not been verified yet.') }}
                        <a class="alert-link" href="{{ route('verification.notice') }}">{{ __('Verify e-mail address') }}</a>
                    </p>
                @endisset
                <x-form.row>
                    <x-form.label for="password">{{ __('New password') }}</x-form.label>
                    <x-form.input name="password" type="password"
                                  aria-describedby="passwordHelpBlock"
                                  autocomplete="new-password" />
                    <div id="passwordHelpBlock" class="form-text">
                        {{ __('Leave empty to keep the current password.') }}
                    </div>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="password_confirmation">{{ __('Confirm password') }}</x-form.label>
                    <x-form.input name="password_confirmation" type="password"
                                  autocomplete="new-password" />
                </x-form.row>
            </div>
            <div class="col-12 col-md-6">
                @include('_shared.address_fields_form', [
                    'address' => $user,
                ])
            </div>
        </div>

        <x-button.save>
            @isset($user){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
        </x-button.save>
    </x-form>

    <section class="mt-3">
        <h2>{{ __('Abilities') }}</h2>
        <div class="mb-3">
            @foreach($user->userRoles as $userRole)
                <span class="badge bg-primary">{{ $userRole->name }}</span>
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
