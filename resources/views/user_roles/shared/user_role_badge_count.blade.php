@php
    /** @var \App\Models\UserRole $userRole */
    $usersCount = $userRole->users()->count();
@endphp
<div class="mb-3">
    <x-bs::badge variant="primary">
        <i class="fa fa-fw fa-users"></i>
        @can('viewAny', \App\Models\UserRole::class)
            <a href="{{ route('users.index', ['filter[user_role_id]' => $userRole->id]) }}" target="_blank" class="text-white">{{ formatTransChoice(':count users', $usersCount) }}</a>
        @else
            {{ formatTransChoice(':count users', $usersCount) }}
        @endif
    </x-bs::badge>
</div>
