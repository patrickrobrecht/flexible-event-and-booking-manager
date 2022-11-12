@props([
    'id' => null,
    'fromName',
    'untilName',
    'untilLabel' => __('until'),
    'fromValue' => null,
    'untilValue' => null,
])

@php
    $id =  $id ?? str_replace('_from', '', $fromName);
    $untilId = $id . '_until';
@endphp
<div class="input-group">
    <x-form.input id="{{ $id }}" name="{{ $fromName }}" {{ $attributes }}
                  :value="$fromValue" />
    <label for="{{ $untilId  }}" class="input-group-text">{{ $untilLabel }}</label>
    <x-form.input id="{{ $untilId }}" name="{{ $untilName }}" {{ $attributes }}
                  :value="$untilValue" />
</div>
