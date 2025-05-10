<x-bs::badge>
    @can('view', $userRole)
        <a href="{{ route('user-roles.show', $userRole) }}" class="text-white">{{ $userRole->name }}</a>
    @else
        {{ $userRole->name }}
    @endcan
</x-bs::badge>
