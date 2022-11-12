@php
    /** @var \App\Options\ActiveStatus $active */
@endphp
@switch($active)
    @case(\App\Options\ActiveStatus::Active)
        <span class="badge bg-success">
            <i class="fa fa-check-circle"></i>
            {{ __('active') }}
        </span>
    @break
    @case(\App\Options\ActiveStatus::Inactive)
        <span class="badge bg-danger">
            <i class="fa fa-power-off"></i>
            {{ __('inactive') }}
        </span>
    @break
    @case(\App\Options\ActiveStatus::Archived)
        <span class="badge bg-dark">
            <i class="fa fa-archive"></i>
            {{ __('archived') }}
        </span>
    @break
@endswitch
