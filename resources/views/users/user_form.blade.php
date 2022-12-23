@extends('layouts.app')

@php
    /** @var ?\App\Models\User $editedUser */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\UserRole[] $userRoles */
@endphp

@section('title')
    @isset($editedUser)
        {{ __('Edit :name', ['name' => $editedUser->name]) }}
    @else
        {{ __('Create user') }}
    @endisset
@endsection

@section('breadcrumbs')
    <x-nav.breadcrumb href="{{ route('users.index') }}">{{ __('Users') }}</x-nav.breadcrumb>
    <x-nav.breadcrumb/>
@endsection

@section('content')
    <x-form method="{{ isset($editedUser) ? 'PUT' : 'POST' }}"
            action="{{ isset($editedUser) ? route('users.update', $editedUser) : route('users.store') }}">
        <div class="row">
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="first_name">{{ __('First name') }}</x-form.label>
                    <x-form.input name="first_name" type="text"
                                  :value="$editedUser->first_name ?? null" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="last_name">{{ __('Last name') }}</x-form.label>
                    <x-form.input name="last_name" type="text"
                                  :value="$editedUser->last_name ?? null" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="email">{{ __('E-mail') }}</x-form.label>
                    <x-form.input name="email" type="email"
                                  :value="$editedUser->email ?? null" />
                </x-form.row>
                @isset($editedUser->email_verified_at)
                    <p class="alert alert-primary">
                        {{ __('The e-mail address has been verified at :email_verified_at', [
                            'email_verified_at' => formatDateTime($editedUser->email_verified_at),
                        ]) }}
                    </p>
                @else
                    <p class="alert alert-danger">
                        {{ __('The e-mail address has not been verified yet.') }}
                    </p>
                @endisset
                <x-form.row>
                    <x-form.label for="password">{{ __('New password') }}</x-form.label>
                    <x-form.input name="password" type="password"
                                  aria-describedby="passwordHelpBlock"
                                  autocomplete="new-password" />
                    <div id="passwordHelpBlock" class="form-text">
                        @isset($editedUser->password)
                            {{ __('Leave empty to keep the current password.') }}
                        @else
                            <span class="fw-bold text-danger">{{ __('No password is currently set for this user.') }}</span>
                        @endisset
                    </div>
                </x-form.row>
                <x-form.row>
                    <x-form.label for="password_confirmation">{{ __('Confirm password') }}</x-form.label>
                    <x-form.input name="password_confirmation" type="password"
                                  autocomplete="new-password" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="user_role_id">{{ __('User role') }}</x-form.label>
                    <x-form.input id="user_role_id" name="user_role_id[]"
                                  type="checkbox"
                                  :options="$userRoles->pluck('name', 'id')"
                                  :value="isset($editedUser) ? $editedUser->userRoles->pluck('id')->toArray() : []"
                                  :valuesToInt="true" />
                </x-form.row>
                <x-form.row>
                    <x-form.label for="status">{{ __('Status') }}</x-form.label>
                    <x-form.select name="status"
                                   :options="\App\Options\ActiveStatus::keysWithNames()"
                                   :value="$editedUser->status->value ?? null" />
                </x-form.row>
            </div>
            <div class="col-12 col-md-6">
                <x-form.row>
                    <x-form.label for="phone">{{ __('Phone number') }}</x-form.label>
                    <x-form.input name="phone" type="tel"
                                  :value="$editedUser->phone ?? null" />
                </x-form.row>
                @include('_shared.address_fields_form', [
                    'address' => $editedUser ?? null,
                ])
            </div>
        </div>

        <x-button.group>
            <x-button.save>
                @isset($editedUser){{ __( 'Save' ) }} @else{{ __('Create') }}@endisset
            </x-button.save>
            <x-button.cancel href="{{ route('users.index') }}"/>
        </x-button.group>
    </x-form>

    <x-text.timestamp :model="$editedUser ?? null" />
@endsection
