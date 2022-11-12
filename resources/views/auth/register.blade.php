@extends('layouts.app')

@section('title')
    {{ __('Register') }}
@endsection

@section('main')
    <x-card.centered>
        <div class="alert alert-primary mb-3">
            <a class="alert-link" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>
        </div>

        <x-form method="POST" action="{{ route('register') }}">
            <x-form.row>
                <x-form.label for="first_name">{{ __('First name') }}</x-form.label>
                <x-form.input name="first_name" type="text" required autofocus />
            </x-form.row>
            <x-form.row>
                <x-form.label for="last_name">{{ __('Last name') }}</x-form.label>
                <x-form.input name="last_name" type="text" required />
            </x-form.row>
            <x-form.row>
                <x-form.label for="email">{{ __('E-mail') }}</x-form.label>
                <x-form.input name="email" type="email" required />
            </x-form.row>
            <x-form.row>
                <x-form.label for="password">{{ __('Password') }}</x-form.label>
                <x-form.input name="password" type="password" required
                              autocomplete="current-password" />
            </x-form.row>
            <x-form.row>
                <x-form.label for="password_confirmation">{{ __('Confirm password') }}</x-form.label>
                <x-form.input name="password_confirmation" type="password" required />
            </x-form.row>
            <x-form.row>
                <x-form.input name="remember" type="checkbox">{{ __('Remember me') }}</x-form.input>
            </x-form.row>
            @php
                $termsAndConditions = config('app.urls.terms_and_conditions');
            @endphp
            @if($termsAndConditions)
                <x-form.row>
                    <x-form.input name="terms_and_conditions" type="checkbox">
                        {!! __('With my registration I accept the :linkStart general terms and conditions:linkEnd.', [
                            'linkStart' => '<a class="alert-link" href="' . $termsAndConditions .'" target="_blank">',
                            'linkEnd' => '</a>',
                        ]) !!}
                    </x-form.input>
                </x-form.row>
            @endif

            <x-form.button class="w-100">{{ __('Register') }}</x-form.button>
        </x-form>
    </x-card.centered>
@endsection
