@if($users->count() === 0)
    <span @class([
        $class,
        'text-danger',
    ])>{{ __('no one assigned') }}</span>
@else
    <span @class([
        $class,
    ])>
        @foreach($users as $user)
            @can('view', $user)
                <a href="{{ route('users.show', $user) }}">{{ $user->name }}</a>@if(!$loop->last), @endif
            @else
                {{ $user->name }}@if(!$loop->last), @endif
            @endcan
        @endforeach
    </span>
@endif

