@props([
    'method' => 'POST',
])
@php
    /** @var string $method */
    $isDefaultMethod = in_array($method, ['GET', 'POST']);
@endphp
<form method="{{ $isDefaultMethod ? $method : 'POST' }}" {{ $attributes }}>
    @if($method !== 'GET')
        @csrf
    @endif

    @if(!$isDefaultMethod)
        @method($method)
    @endif

    {{ $slot }}
</form>
