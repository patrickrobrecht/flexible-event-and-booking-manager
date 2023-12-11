@php
    /** @var \App\Options\Visibility $visibility */
@endphp
@switch($visibility)
    @case(\App\Options\Visibility::Public)
        <x-bs::badge variant="success">
            <i class="fa fa-fw fa-lock-open"></i>
            {{ __('public') }}
        </x-bs::badge>
    @break
    @case(\App\Options\Visibility::Private)
        <x-bs::badge variant="danger">
            <i class="fa fa-fw fa-lock"></i>
            {{ __('private') }}
        </x-bs::badge>
    @break
@endswitch
