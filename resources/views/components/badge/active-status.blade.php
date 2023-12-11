@php
    /** @var \App\Options\ActiveStatus $active */
@endphp
@switch($active)
    @case(\App\Options\ActiveStatus::Active)
        <x-bs::badge variant="success">
            <i class="fa fa-check-circle"></i>
            {{ __('active') }}
        </x-bs::badge>
    @break
    @case(\App\Options\ActiveStatus::Inactive)
        <x-bs::badge variant="danger">
            <i class="fa fa-power-off"></i>
            {{ __('inactive') }}
        </x-bs::badge>
    @break
    @case(\App\Options\ActiveStatus::Archived)
        <x-bs::badge variant="dark">
            <i class="fa fa-archive"></i>
            {{ __('archived') }}
        </x-bs::badge>
    @break
@endswitch
