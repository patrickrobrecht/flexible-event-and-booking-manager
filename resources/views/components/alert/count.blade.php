@props([
    'count' => 0,
])
@if($count === 0)
    <p {{ $attributes->class('alert alert-danger') }}>
        {{ trans_choice(':count results found.', $count) }}
    </p>
@else
    <p {{ $attributes->class('alert alert-success') }}>
        {{ trans_choice(':count results found.', $count, ['count' => formatInt($count)]) }}
    </p>
@endif
