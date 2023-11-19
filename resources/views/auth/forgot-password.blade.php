@extends('layouts.app')

@section('title')
    {{ __('Forgot password') }}
@endsection

@section('main')
    <x-card.centered>
        <x-bs::form method="POST" action="{{ route('password.email') }}">
            <x-bs::form.field name="email" type="email" required autofocus>{{ __('E-mail') }}</x-bs::form.field>
            <x-form.button class="w-100">{{ __('E-mail password reset link') }}</x-form.button>
        </x-bs::form>
    </x-card.centered>
@endsection
