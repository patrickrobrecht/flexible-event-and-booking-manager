@extends('layouts.app')

@section('title')
    {{ __('Reset password') }}
@endsection

@section('main')
    <x-card.centered>
        <x-form method="POST" action="{{ route('password.update') }}">
            {{-- Password Reset Token --}}
            <input type="hidden" name="token" value="{{ $request->route('token') }}">

            <x-form.row>
                <x-form.label for="email">{{ __('E-mail') }}</x-form.label>
                <x-form.input name="email" type="email" required autofocus
                              :value="$request->email ?? null" />
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

            <x-form.button class="w-100">{{ __('Reset password') }}</x-form.button>
        </x-form>
    </x-card.centered>
@endsection
