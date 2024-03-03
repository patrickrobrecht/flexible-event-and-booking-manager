<?php

namespace App\Livewire\Groups;

use App\Livewire\Forms\GroupForm;
use App\Models\Group;
use Illuminate\View\View;
use Livewire\Component;

class EditGroup extends Component
{
    public Group $group;
    public GroupForm $form;

    public function mount(Group $group): void
    {
        $this->group = $group;
        $this->form->setGroup($group);
    }

    public function save(): void
    {
        if ($this->form->update()) {
            /** Call @see ManageGroups::updateGroup() */
            $this->dispatch('group-updated', group: $this->group);
        }
    }

    public function render(): View
    {
        return view('livewire.groups.edit-group');
    }
}
