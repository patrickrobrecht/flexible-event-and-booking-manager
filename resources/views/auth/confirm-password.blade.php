@extends('layouts.app')

@section('title')
    {{ __('Confirm password') }}
@endsection

@section('main')
    <x-card.centered>
        <div class="alert alert-primary mb-3">
            {{ __('This is a secure area of the application. Please confirm your password before continuing.') }}
        </div>

        <x-form method="POST" action="{{ route('password.confirm') }}">
            <x-form.row>
                <x-form.label for="password">{{ __('Password') }}</x-form.label>
                <x-form.input name="password" type="password" required
                              autocomplete="current-password" />
            </x-form.row>

            <x-form.button class="w-100">{{ __('Confirm') }}</x-form.button>
        </x-form>
    </x-card.centered>
@endsection
