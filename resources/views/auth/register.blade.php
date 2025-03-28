@extends('layouts.app')

@section('title')
    {{ __('Register') }}
@endsection

@section('main')
    <x-card.centered>
        <x-bs::alert variant="primary" class="mb-3">
            <a class="alert-link" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
        </x-bs::alert>

        <x-bs::form method="POST" action="{{ route('register') }}">
            <x-bs::form.field name="first_name" type="text" required autofocus>{{ __('First name') }}</x-bs::form.field>
            <x-bs::form.field name="last_name" type="text" required>{{ __('Last name') }}</x-bs::form.field>
            <x-bs::form.field name="email" type="email" required>{{ __('E-mail') }}</x-bs::form.field>
            <x-bs::form.field name="password" type="password" required
                              autocomplete="current-password">{{ __('Password') }}</x-bs::form.field>
            <x-bs::form.field name="password_confirmation" type="password" required>{{ __('Confirm password') }}</x-bs::form.field>
            @php
                $termsAndConditions = config('app.urls.terms_and_conditions');
            @endphp
            @if($termsAndConditions)
                @php
                    $option = \Portavice\Bladestrap\Support\Options::one(
                        __('With my registration I accept the :linkStart general terms and conditions:linkEnd.', [
                            'linkStart' => '<a class="alert-link" href="' . $termsAndConditions .'" target="_blank">',
                            'linkEnd' => '</a>',
                        ])
                    );
                @endphp
                <x-bs::form.field name="terms_and_conditions" type="checkbox" :options="$option" :allow-html="true"/>
            @endif

            <x-bs::button @class([
                'mt-3' => $termsAndConditions,
                'w-100',
            ])>{{ __('Register') }}</x-bs::button>
        </x-bs::form>
    </x-card.centered>
@endsection
