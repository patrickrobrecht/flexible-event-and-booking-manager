@extends('layouts.app')

@section('title')
    {{ __('Login') }}
@endsection

@section('main')
    <x-card.centered>
        @if(config('app.features.registration'))
            <div class="alert alert-primary mb-3">
                {{ __('Not registered yet?') }}

                <a class="alert-link" href="{{ route('register') }}">
                    {{ __('Register') }}
                </a>
            </div>
        @endif

        <x-form method="POST" action="{{ route('login') }}">
            <x-form.row>
                <x-form.label for="email">{{ __('E-mail') }}</x-form.label>
                <x-form.input name="email" type="email" required autofocus />
            </x-form.row>
            <x-form.row>
                <x-form.label for="password">{{ __('Password') }}</x-form.label>
                <x-form.input name="password" type="password" required
                              autocomplete="current-password" />
            </x-form.row>
            <x-form.row>
                <x-form.input name="remember" type="checkbox">{{ __('Remember me') }}</x-form.input>
            </x-form.row>

            <x-form.button class="w-100">{{ __('Login') }}</x-form.button>

            @if (\Illuminate\Support\Facades\Route::has('password.request'))
                <div class="small mt-5">
                    <a href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                </div>
            @endif
        </x-form>
    </x-card.centered>
@endsection
