@extends('layouts.app')

@php
    /** @var ?\Laravel\Sanctum\PersonalAccessToken $token */
@endphp

@section('title')
    @isset($token)
        {{ __('Edit :name', ['name' => $token->name]) }}
    @else
        {{ __('Create personal access token') }}
    @endisset
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('personal-access-tokens.index') }}">{{ __('Personal access tokens') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-form method="{{ isset($token) ? 'PUT' : 'POST' }}"
            action="{{ isset($token) ? route('personal-access-tokens.update', $token) : route('personal-access-tokens.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="name">{{ __('Name') }}</x-form.label>
                    <x-form.input name="name" type="text"
                                  :value="$token->name ?? null" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="expires_at">
                        {{ __('Expires at') }}
                        @if(isset($token->expires_at) && $token->expires_at->isPast())
                            <span class="badge bg-danger">{{ __('expired') }}</span>
                        @endif
                    </x-form.label>
                    <x-form.input name="expires_at" aria-describedby="expireHelpBlock"
                                  type="datetime-local"
                                  :value="isset($token->expires_at) ? $token->expires_at->format('Y-m-d\TH:i') : null" />
                    <div id="expireHelpBlock" class="form-text">
                        {{ __('Last used') }}: {{ isset($token->last_used_at) ? formatDateTime($token->last_used_at) : __('never') }}
                    </div>
                </x-form.row>
            </div>
        </div>
        <x-form.row>
            <x-form.label for="abilities">{{ __('Abilities') }}</x-form.label>
            <div class="cols-lg-2 cols-xl-3 cols-xxl-4">
                <x-form.input for="abilities" name="abilities[]" type="checkbox"
                              :options="\App\Options\Ability::keysWithNames()"
                              :value="$token->abilities ?? []" />
            </div>
        </x-form.row>

        <x-button.group>
            <x-button.save>
                @isset($token){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
            </x-button.save>
            <x-button.cancel href="{{ route('personal-access-tokens.index') }}"/>
        </x-button.group>
    </x-form>

    <x-text.timestamp :model="$token ?? null" />
@endsection
