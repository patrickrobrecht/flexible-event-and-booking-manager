@props(['value'])

<label {{ $attributes->merge(['class' => 'form-label']) }}>
    {{ $slot }}
</label>
