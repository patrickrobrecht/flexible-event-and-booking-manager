@extends('layouts.app')

@section('title')
    {{ __('Verify e-mail address') }}
@endsection

@section('main')
    <x-card.centered>
        <x-bs::alert variant="primary" class="mb-3">
            {{ __("Before getting started, could you verify your e-mail address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}
        </x-bs::alert>

        <x-bs::form method="POST" action="{{ route('verification.send') }}">
            <x-form.button class="w-100">{{ __('Resend verification e-mail') }}</x-form.button>
        </x-bs::form>
    </x-card.centered>
@endsection
