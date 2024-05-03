@if($users->count() === 0)
    <x-bs::alert variant="danger">{{ __('No responsible persons have been assigned.') }}</x-bs::alert>
@else
    <x-bs::list>
        @foreach($users as $user)
            <x-bs::list.item>
                @can('view', $user)
                    <a href="{{ route('users.show', $user) }}">{{ $user->name }}</a>
                @else
                    {{ $user->name }}
                @endcan
            </x-bs::list.item>
        @endforeach
    </x-bs::list>
@endif

