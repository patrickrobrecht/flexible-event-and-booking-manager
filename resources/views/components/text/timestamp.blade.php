@props([
    'model',
])
@php
    /** @var ?\App\Models\Traits\HasTimestamps $model */
@endphp
@isset($model)
    <div {{ $attributes->class('mt-2 small') }}>
        {{ __('Created at :created, last updated at :updated.', [
            'created' => formatDateTime($model->created_at),
            'updated' => formatDateTime($model->updated_at),
        ]) }}
    </div>
@endisset
