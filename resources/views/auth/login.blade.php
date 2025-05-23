@extends('layouts.app')

@section('title')
    {{ __('Login') }}
@endsection

@section('main')
    <x-card.centered>
        @if(config('app.features.registration'))
            <x-bs::alert variant="primary" class="mb-3">
                {{ __('Not registered yet?') }}

                <a class="alert-link" href="{{ route('register') }}">
                    {{ __('Register') }}
                </a>
            </x-bs::alert>
        @endif

        <x-bs::form method="POST" action="{{ route('login') }}">
            <x-bs::form.field name="email" type="email" required autofocus>{{ __('E-mail') }}</x-bs::form.field>
            <x-bs::form.field name="password" type="password" required
                              autocomplete="current-password">{{ __('Password') }}</x-bs::form.field>
            <x-bs::form.field name="remember" type="checkbox" :options="\Portavice\Bladestrap\Support\Options::one(__('Remember me'))"/>
            <x-bs::button class="mt-3 w-100">{{ __('Login') }}</x-bs::button>

            @if (\Illuminate\Support\Facades\Route::has('password.request'))
                <div class="small mt-3">
                    <a href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                </div>
            @endif
        </x-bs::form>
    </x-card.centered>
@endsection
