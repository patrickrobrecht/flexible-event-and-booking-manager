@props([
    'id',
    'name',
    'prependText' => null,
    'appendText' => null,
    'value' => null,
    'options' => [],
    'attributesForOption' => [],
    'errorBag' => $errors,
])
@php
    if (count(old()) > 0) {
        $old = old(fieldNameToArray($name));
        if (!is_array($old)) {
            $value = $old;
        }
    } elseif (\App\Helpers\QueryInput::hasAnyOrDefault()) {
        $oldQuery = \App\Helpers\QueryInput::old(fieldNameToArray($name));
        if (!is_array($oldQuery)) {
            $value = $oldQuery;
        }
    }
    if (is_numeric($value)) {
        $value = (int) $value;
    }
@endphp
@if($prependText || $appendText)
    <div class="input-group has-validation">
@endif
        @if($prependText)
            <label for="{{ $id ?? $name }}" class="input-group-text">
                {{ $prependText }}
            </label>
        @endif
        <select id="{{ $id ?? $name }}" name="{{ $name }}"
                {{ $attributes->class(['form-select', 'is-invalid' => $errorBag->hasAny(fieldNameToArray($name))]) }}>

            {{ $slot }}

            @foreach($options as $optionValue => $optionLabel)
                <x-form.select-option :value="$optionValue" :label="$optionLabel" :selectedValue="$value"
                                      :otherAttributes="$attributesForOption[$optionValue] ?? []"/>
            @endforeach
        </select>
        @if($appendText)
            <label for="{{ $id ?? $name }}" class="input-group-text">
                {{ $appendText }}
            </label>
        @endif
        <x-form.feedback name="{{ $name }}" :errorBag="$errorBag"/>
@if($prependText || $appendText)
    </div>
@endif
