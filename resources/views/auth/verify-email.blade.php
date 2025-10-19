@extends('layouts.app')

@section('title')
    {{ __('Verify e-mail address') }}
@endsection

@section('main')
    <x-card.centered>
        @include('account.shared.unverified_email')
    </x-card.centered>
@endsection
