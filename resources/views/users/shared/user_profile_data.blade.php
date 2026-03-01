@php
    /** @var \App\Models\User $user */
@endphp
<div class="my-1">
    <span class="text-nowrap me-3"><x-badge.enum :case="$user->status"/></span>
    <span class="text-nowrap me-3"><i class="fa fa-fw fa-sign-in-alt"></i> {{ __('Last login') }}
        {{ $user->last_login_at ? formatDateTime($user->last_login_at) : __('never') }}</span>
    @if($user->userRoles->isNotEmpty())
        <span class="me-3">
            <i class="fa fa-fw fa-user-group" title="{{ __('User roles') }}"></i>
            @foreach($user->userRoles->sortBy('name') as $userRole)
                @include('user_roles.shared.user_role_badge_link')
            @endforeach
        </span>
    @endif
</div>
<div class="mb-3">
    @isset($user->date_of_birth)
        <span class="text-nowrap me-3"><i class="fa fa-fw fa-cake-candles" title="{{ __('Date of birth') }}"></i> {{ formatDate($user->date_of_birth) }}</span>
    @endisset
    @isset($user->phone)
        <span class="text-nowrap me-3"><i class="fa fa-fw fa-phone" title="{{ __('Phone number') }}"></i> <a href="{{ $user->phone_link }}">{{ $user->phone }}</a></span>
    @endisset
    <span class="text-nowrap me-3"><i class="fa fa-fw fa-at" title="{{ __('E-mail') }}"></i> <a href="mailto:{{ $user->email }}">{{ $user->email }}</a>
        @isset($user->email_verified_at)
            <x-bs::badge variant="success">{{ __('verified') }}</x-bs::badge>
        @else
            <x-bs::badge variant="danger">{{ __('not verified') }}</x-bs::badge>
        @endisset
    </span>
    @if(count($user->addressBlock) > 0)
        <span class="text-nowrap me-3"><i class="fa fa-fw fa-location-pin" title="{{ __('Address') }}"></i> {{ implode(', ', $user->addressBlock) }}</span>
    @endif
</div>
