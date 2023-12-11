@props([
    'count' => 0,
])
@if($count === 0)
    <x-bs::alert variant="danger" :attributes="$attributes">{{ trans_choice(':count results found.', $count) }}</x-bs::alert>
@else
    <x-bs::alert variant="success" :attributes="$attributes">{{ trans_choice(':count results found.', $count, ['count' => formatInt($count)]) }}</x-bs::alert>
@endif
