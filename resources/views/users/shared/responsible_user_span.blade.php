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
            @php
                $positionAndComma = (isset($user->pivot->position) ? sprintf(' (%s)', $user->pivot->position) : '')
                    . ($loop->last ? '' : ', ');
            @endphp
            <span class="text-nowrap">
                @can('view', $user)
                    <a href="{{ route('users.show', $user) }}">{{ $user->name }}</a>{{ $positionAndComma }}
                @else
                    {{ $user->name }}{{ $positionAndComma }}
                @endcan
            </span>
        @endforeach
    </span>
@endif

