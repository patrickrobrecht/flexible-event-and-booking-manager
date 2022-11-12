@extends('layouts.app')

@section('title')
    {{ __('Forgot password') }}
@endsection

@section('main')
    <x-card.centered>
        <x-form method="POST" action="{{ route('password.email') }}">
            <x-form.row>
                <x-form.label for="email">{{ __('E-mail') }}</x-form.label>
                <x-form.input name="email" type="email" required autofocus />
            </x-form.row>

            <x-form.button class="w-100">{{ __('E-mail password reset link') }}</x-form.button>
        </x-form>
    </x-card.centered>
@endsection
