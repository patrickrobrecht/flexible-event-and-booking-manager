@extends('layouts.app')

@section('title')
    {{ __('Confirm password') }}
@endsection

@section('main')
    <x-card.centered>
        <x-bs::alert variant="primary" class="mb-3">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </x-bs::alert>

        <x-bs::form method="POST" action="{{ route('password.confirm') }}">
            <x-bs::form.field name="password" type="password" required
                              autocomplete="current-password">{{ __('Password') }}</x-bs::form.field>
            <x-form.button class="w-100">{{ __('Confirm') }}</x-form.button>
        </x-bs::form>
    </x-card.centered>
@endsection
