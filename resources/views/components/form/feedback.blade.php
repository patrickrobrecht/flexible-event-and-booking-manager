@props([
    'name',
    'errorBag' => $errors,
    'showSubErrors' => false,
])
@php
    $fieldName = fieldNameToArray($name);
@endphp
@if($errorBag->hasAny([$fieldName]))
    <span role="alert" {{ $attributes->class('invalid-feedback') }}>
        @foreach($errorBag->get($fieldName) as $error)
            <strong>{{ $error }}</strong>
        @endforeach
    </span>
@endif
@if($showSubErrors)
    @if($errorBag->hasAny($fieldName . '.*'))
        <span role="alert" {{ $attributes->class('invalid-feedback') }}>
            @foreach($errorBag->get($fieldName . '.*') as $errorForItem)
                @foreach($errorForItem as $error)
                    <strong>{{ $error }}</strong>
                @endforeach
            @endforeach
        </span>
    @endif
@endif
