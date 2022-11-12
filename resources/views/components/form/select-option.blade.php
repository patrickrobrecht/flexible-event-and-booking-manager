@props([
    'value',
    'label',
    'selectedValue',
    'otherAttributes' => [],
])
<option {{ $attributes->merge(['value' => $value, 'selected' => $value === $selectedValue])->merge($otherAttributes) }}>
    {{ $label }}
</option>
