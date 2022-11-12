@props([
    'model',
])
<small class="text-muted" title="{{ formatDateTime($model->updated_at) }}">
    {{ __('Last updated :updated_diff', ['updated_diff' => $model->updated_at->diffForHumans()]) }}
</small>
