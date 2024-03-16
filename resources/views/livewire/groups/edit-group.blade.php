<form wire:submit>
    <x-bs::form.field name="form.name" type="text" maxlength="255"
                      wire:model.blur="form.name" wire:dirty.class="bg-warning-subtle">{{ __('Name') }}</x-bs::form.field>
    <x-bs::form.field name="form.description" type="textarea" maxlength="255"
                      wire:model.blur="form.description" wire:dirty.class="bg-warning-subtle">{{ __('Description') }}</x-bs::form.field>
</form>
