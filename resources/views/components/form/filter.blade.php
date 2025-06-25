@props([
    'id' => 'filter',
    'showByDefault' => true,
    'addButtons',
])
@php
    $show = $showByDefault
        || \Portavice\Bladestrap\Support\ValueHelper::hasAnyFromQueryOrDefault()
        || $errors->any();
@endphp
<x-bs::form id="{{ $id }}" method="GET" {{ $attributes->class(['my-3', 'collapse', 'show' => $show]) }}>
    {{ $slot }}

    <div class="d-flex flex-wrap gap-1">
        <x-bs::button type="submit" variant="outline-primary">
            <i class="fa fa-fw fa-search"></i>
            {{ __('Search and filter') }}
        </x-bs::button>
        @isset($addButtons)
            {{ $addButtons }}
        @endisset
        <x-bs::button.link variant="secondary"
                           href="{{ route(\Illuminate\Support\Facades\Route::currentRouteName(), \Illuminate\Support\Facades\Route::current()->parameters()) }}">
            <i class="fa fa-fw fa-undo"></i>
            {{ __('Reset') }}
        </x-bs::button.link>
    </div>
</x-bs::form>
