@extends('layouts.app')

@php
    use App\Enums\FilterValue;
    use Portavice\Bladestrap\Support\Options;

    /** @var \Illuminate\Pagination\LengthAwarePaginator|\App\Models\User[] $users */
    /** @var \Illuminate\Database\Eloquent\Collection|\App\Models\UserRole[] $userRoles */
@endphp

@section('title')
    {{ __('Users') }}
@endsection

@section('breadcrumbs')
    <x-bs::breadcrumb.item>@yield('title')</x-bs::breadcrumb.item>
@endsection

@section('content')
    <x-bs::button.group>
        @can('create', \App\Models\User::class)
            <x-button.create href="{{ route('users.create') }}">
                {{ __('Create user') }}
            </x-button.create>
        @endcan
    </x-bs::button.group>

    <x-form.filter>
        <div class="row">
            <div class="col-12 col-sm-6 col-xl-3">
                <x-bs::form.field id="name" name="filter[name]" type="text"
                                  :from-query="true">{{ __('Name') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-bs::form.field id="email" name="filter[email]" type="text"
                                  :from-query="true"><i class="fa fa-fw fa-at"></i> {{ __('E-mail') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-bs::form.field id="user_role_id" name="filter[user_role_id]" type="select"
                                  :options="Options::fromModels($userRoles, 'name')->prependMany(\App\Models\UserRole::filterOptions())"
                                  :cast="FilterValue::castToIntIfNoValue()"
                                  :from-query="true"><i class="fa fa-fw fa-user-group"></i> {{ __('User role') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-sm-6 col-xl-3">
                <x-bs::form.field id="status" name="filter[status]" type="select"
                                  :options="\App\Enums\ActiveStatus::toOptionsWithAll()"
                                  :cast="FilterValue::castToIntIfNoValue()"
                                  :from-query="true"><i class="fa fa-fw fa-circle-question"></i> {{ __('Status') }}</x-bs::form.field>
            </div>
            <div class="col-12 col-lg-6 col-xl-3">
                <x-bs::form.field name="sort" type="select"
                                  :options="\App\Models\User::sortOptions()->getNamesWithLabels()"
                                  :from-query="true"><i class="fa fa-fw fa-sort"></i> {{ __('Sorting') }}</x-bs::form.field>
            </div>
        </div>
    </x-form.filter>

    <x-alert.count class="mt-3" :count="$users->total()"/>

    <div class="row my-3">
        @foreach($users as $user)
            @php
                $showRouteUrl = route('users.show', $user);
            @endphp
            <div class="col-12 col-lg-6 col-xxl-4 mb-3">
                <div class="card">
                    <div class="card-header">
                        <h2 class="card-title">
                            @can('view', $user)
                                <a href="{{ $showRouteUrl }}">{{ $user->name }}</a>
                            @else
                                {{ $user->name }}
                            @endcan
                        </h2>
                    </div>
                    <x-bs::list :flush="true">
                        @isset($user->date_of_birth)
                            <x-bs::list.item>
                                <span class="text-nowrap"><i class="fa fa-fw fa-cake-candles"></i> {{ __('Date of birth') }}</span>
                                <x-slot:end>{{ formatDate($user->date_of_birth) }}</x-slot:end>
                            </x-bs::list.item>
                        @endisset
                        @isset($user->phone)
                            <x-bs::list.item>
                                <span class="text-nowrap"><i class="fa fa-fw fa-phone"></i> {{ __('Phone number') }}</span>
                                <x-slot:end>
                                    <span class="text-end"><a href="{{ $user->phone_link }}">{{ $user->phone }}</a></span>
                                </x-slot:end>
                            </x-bs::list.item>
                        @endisset
                        <x-bs::list.item>
                            <span class="text-nowrap"><i class="fa fa-fw fa-at"></i> {{ __('E-mail') }}</span>
                            <x-slot:end>
                                <span class="text-end">
                                    <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
                                    @isset($user->email_verified_at)
                                        <x-bs::badge variant="success">{{ __('verified') }}</x-bs::badge>
                                    @else
                                        <x-bs::badge variant="danger">{{ __('not verified') }}</x-bs::badge>
                                    @endisset
                                </span>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span class="text-nowrap"><i class="fa fa-fw fa-circle-question"></i> {{ __('Status') }}</span>
                            <x-slot:end>
                                <span class="text-end">
                                    <x-badge.active-status :active="$user->status"/>
                                </span>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span class="text-nowrap"><i class="fa fa-fw fa-user-group"></i> {{ __('User roles') }}</span>
                            <x-slot:end>
                                <span class="text-end">
                                    @if($user->userRoles->count() === 0)
                                        {{ __('none') }}
                                    @else
                                        @foreach($user->userRoles->sortBy('name') as $userRole)
                                            @include('user_roles.shared.user_role_badge_link')
                                        @endforeach
                                    @endif
                                </span>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span class="text-nowrap"><i class="fa fa-fw fa-sign-in-alt"></i> {{ __('Last login') }}</span>
                            <x-slot:end>
                                <span class="text-end">
                                    {{ $user->last_login_at ? formatDateTime($user->last_login_at) : __('never') }}
                                </span>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span class="text-nowrap"><i class="fa fa-fw fa-file-contract"></i> <a href="{{ $showRouteUrl }}#bookings">{{ __('Bookings') }}</a></span>
                            <x-slot:end>
                                <span>
                                    <x-bs::badge>{{ formatInt($user->bookings_count) }}</x-bs::badge>
                                    @if($user->bookings_trashed_count > 0)
                                        <x-bs::badge variant="danger">+&nbsp;{{ formatInt($user->bookings_trashed_count) }} {{ __('trashed') }}</x-bs::badge>
                                    @endif
                                </span>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span class="text-nowrap"><i class="fa fa-fw fa-file"></i> <a href="{{ $showRouteUrl }}#documents">{{ __('Documents') }}</a></span>
                            <x-slot:end>
                                <x-bs::badge>{{ formatInt($user->documents_count) }}</x-bs::badge>
                            </x-slot:end>
                        </x-bs::list.item>
                        <x-bs::list.item>
                            <span class="text-nowrap"><i class="fa fa-fw fa-list-check"></i> <a href="{{ $showRouteUrl }}#responsibilities">{{ __('Responsibilities') }}</a></span>
                            <x-slot:end>
                                <span class="text-end ms-2">
                                    <x-bs::badge>{{ formatTransChoice(':count organizations', $user->responsible_for_organizations_count) }}</x-bs::badge>
                                    <x-bs::badge>{{ formatTransChoice(':count event series', $user->responsible_for_event_series_count) }}</x-bs::badge>
                                    <x-bs::badge>{{ formatTransChoice(':count events', $user->responsible_for_events_count) }}</x-bs::badge>
                                </span>
                            </x-slot:end>
                        </x-bs::list.item>
                    </x-bs::list>
                    @canany(['update', 'forceDelete'], $user)
                        <div class="card-body">
                            @can('update', $user)
                                <x-button.edit href="{{ route('users.edit', $user) }}"/>
                            @endcan
                            @can('forceDelete', $user)
                                @php
                                    $hint = null;
                                    if ($user->tokens_count) {
                                        $hint = formatTransChoice(':name has :count personal access tokens which will be deleted with the account.', $user->tokens_count, [
                                            'name' => $user->name,
                                        ]);
                                    }
                                @endphp
                                <x-form.delete-modal :id="$user->id"
                                                     :name="$user->name"
                                                     :hint="$hint"
                                                     :route="route('users.destroy', $user)"/>
                            @endcan
                        </div>
                    @endcanany
                    <div class="card-footer">
                        <x-text.updated-human-diff :model="$user"/>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{ $users->withQueryString()->links() }}
@endsection
