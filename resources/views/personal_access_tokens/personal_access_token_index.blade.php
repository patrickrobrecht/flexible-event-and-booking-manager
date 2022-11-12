@extends('layouts.app')

@php
    $user = \Illuminate\Support\Facades\Auth::user();
    $tokens = $user->tokens;

    /** @var \App\Models\PersonalAccessToken $token */
@endphp

@section('title')
    {{ __('Personal access tokens') }}
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-button.group>
        @can('create', \App\Models\PersonalAccessToken::class)
            <x-button.create href="{{ route('personal-access-tokens.create') }}">
                {{ __('Create personal access token') }}
            </x-button.create>
        @endcan
    </x-button.group>

    <x-alert.count class="mt-3" :count="$user->tokens->count()" />

    <div class="row my-3">
        @foreach($user->tokens as $token)
            <div class="col-12 col-sm-6 col-md-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">{{ $token->name }}</h2>
                        <p class="card-subtitle text-muted">
                            {{ __('Last used') }}: {{ $token->last_used_at ? formatDateTime($token->last_used_at) : __('never') }}
                            @if(isset($token->expires_at) && $token->expires_at->isPast())
                                <span class="badge bg-danger">{{ __('expired') }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="card-body">
                        @can('update', $token)
                            <x-button.edit href="{{ route('personal-access-tokens.edit', $token) }}"/>
                        @endcan
                        @can('forceDelete', $token)
                            <x-button.delete form="delete-{{ $token->id }}" />
                            <x-form id="delete-{{ $token->id }}"
                                    method="DELETE" action="{{ route('personal-access-tokens.destroy', $token) }}" />
                        @endcan
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
