@php
    /** @var \App\Options\Visibility $visibility */
@endphp
@switch($visibility)
    @case(\App\Options\Visibility::Public)
        <span class="badge bg-success">
            <i class="fa fa-fw fa-lock-open"></i>
            {{ __('public') }}
        </span>
    @break
    @case(\App\Options\Visibility::Private)
        <span class="badge bg-danger">
            <i class="fa fa-fw fa-lock"></i>
            {{ __('private') }}
        </span>
    @break
@endswitch
