@php
    use App\Models\User;

    /** @var ?User $user */
@endphp
@can('viewAny', User::class)
    <x-bs::breadcrumb.item href="{{ route('users.index') }}">{{ __('Users') }}</x-bs::breadcrumb.item>
@else
    <x-bs::breadcrumb.item>{{ __('Users') }}</x-bs::breadcrumb.item>
@endcan
@isset($user)
    @can('view', $user)
        <x-bs::breadcrumb.item href="{{ route('users.show', $user) }}">{{ $user->name }}</x-bs::breadcrumb.item>
    @else
        <x-bs::breadcrumb.item>{{ $user->name }}</x-bs::breadcrumb.item>
    @endcan
@endisset
