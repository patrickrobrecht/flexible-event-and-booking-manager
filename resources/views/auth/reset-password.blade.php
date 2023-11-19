@extends('layouts.app')

@section('title')
    {{ __('Reset password') }}
@endsection

@section('main')
    <x-card.centered>
        <x-bs::form method="POST" action="{{ route('password.update') }}">
            <input type="hidden" name="token" value="{{ $request->route('token') }}">
            <x-bs::form.field name="email" type="email" required autofocus
                              :value="$request->email ?? null">{{ __('E-mail') }}</x-bs::form.field>
            <x-bs::form.field name="password" type="password" required
                              autocomplete="current-password">{{ __('Password') }}</x-bs::form.field>
            <x-bs::form.field name="password_confirmation" type="password" required>{{ __('Confirm password') }}</x-bs::form.field>
            <x-form.button class="w-100">{{ __('Reset password') }}</x-form.button>
        </x-bs::form>
    </x-card.centered>
@endsection
