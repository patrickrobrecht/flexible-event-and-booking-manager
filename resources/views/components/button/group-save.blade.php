@props([
    'disableSave' => false,
    'showCreate' => false,
    'indexRoute' => null,
])
@php
    /** @var bool $disableSave */
    /** @var bool $showCreate */
    /** @var ?string $indexRoute */
@endphp
<x-bs::button.group>
    <x-bs::button :disabled="$disableSave">
        <i class="fa fa-fw fa-save"></i> {{ __('Save') }}
        <span class="d-block small"><i class="fa fa-fw fa-list"></i> {{ __('and back to overview') }}</span>
    </x-bs::button>
    @if(!$disableSave)
        @if($showCreate)
            <x-bs::button name="action" value="create" variant="secondary">
                <i class="fa fa-fw fa-save"></i> {{ __('Save') }}
                <span class="d-block small"><i class="fa fa-fw fa-plus"></i> {{ __('and create more') }}</span>
            </x-bs::button>
        @else
            <x-bs::button name="action" value="edit" variant="secondary">
                <i class="fa fa-fw fa-save"></i> {{ __('Save') }}
                <span class="d-block small"><i class="fa fa-fw fa-plus"></i> {{ __('and continue editing') }}</span>
            </x-bs::button>
        @endif
    @endif
    @isset($indexRoute)
        <x-bs::button.link variant="danger" href="{{ $indexRoute }}">
            <i class="fa fa-fw fa-window-close"></i> {{ __('Discard') }}
            <span class="d-block small"><i class="fa fa-fw fa-list"></i> {{ __('and back to overview') }}</span>
        </x-bs::button.link>
    @endisset
</x-bs::button.group>
