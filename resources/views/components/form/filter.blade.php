@props([
    'id' => 'filter',
    'showByDefault' => true,
    'addButtons',
])
@php
    $show = $showByDefault || \App\Helpers\QueryInput::hasAny() || $errors->any();
@endphp
<form id="{{ $id }}" method="GET" {{ $attributes->class(['my-3', 'collapse', 'show' => $show]) }}>
    {{ $slot }}

    <x-button.group>
        <button type="submit" class="btn btn-outline-primary">
            <i class="fa fa-search"></i>
            {{ __('Search and filter') }}
        </button>
        @isset($addButtons)
            {{ $addButtons }}
        @endisset
        <a href="{{ route(\Illuminate\Support\Facades\Route::currentRouteName(), \Illuminate\Support\Facades\Route::current()->parameters()) }}"
           class="btn btn-secondary">
            <i class="fa fa-undo"></i>
            {{ __('Reset') }}
        </a>
    </x-button.group>
</form>
