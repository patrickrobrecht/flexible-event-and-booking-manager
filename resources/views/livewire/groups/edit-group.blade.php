<form wire:submit="save">
    <x-bs::form.field name="form.name" type="text" maxlength="255" wire:model="form.name">{{ __('Name') }}</x-bs::form.field>
    <x-bs::form.field name="form.description" type="textarea" maxlength="255" wire:model="form.description">{{ __('Description') }}</x-bs::form.field>
    <x-bs::button><i class="fa fa-save"></i> {{ __('Save') }}</x-bs::button>
</form>
