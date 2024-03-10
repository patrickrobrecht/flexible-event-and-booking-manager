<?php

namespace App\Livewire\Forms;

use App\Models\Event;
use App\Models\Group;
use Illuminate\Validation\Rule;
use Livewire\Form;

class GroupForm extends Form
{
    public ?Group $group;
    public ?string $name = null;
    public ?string $description = null;

    public function setGroup(Group $group): void
    {
        $this->group = $group;
        $this->name = $group->name;
        $this->description = $group->description;
    }

    public function update(): bool
    {
        $validated = $this->validateGroupForEvent($this->group->event);
        return $this->group->fill($validated)->save();
    }

    public function validateGroupForEvent(Event $event): array
    {
        $uniqueRule = Rule::unique('groups', 'name')
            ->where('event_id', $event->id);
        if (isset($this->group)) {
            $uniqueRule->ignoreModel($this->group);
        }

        return $this->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                $uniqueRule,
            ],
            'description' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);
    }
}
