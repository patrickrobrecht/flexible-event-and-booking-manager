<?php

namespace Tests\Feature\Livewire\Groups;

use App\Livewire\Groups\EditGroup;
use App\Models\Event;
use App\Models\Group;
use App\Models\Location;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class EditGroupTest extends TestCase
{
    use RefreshDatabase;

    public function testComponentRendersCorrectly(): void
    {
        Livewire::test(EditGroup::class)
            ->assertStatus(200);
    }

    public function testComponentUpdatesGroup(): void
    {
        $group = $this->fakeGroupWithEventAndSiblingGroup();
        $this->assertNull($group->description);

        Livewire::test(EditGroup::class, ['group' => $group])
            ->set('form.description', 'Test Description')
            ->call('save');

        $group->refresh();
        $this->assertEquals('Test Description', $group->description);

        Livewire::test(EditGroup::class, ['group' => $group])
            ->set('form.name', 'Another Name')
            ->set('form.description')
            ->call('save');
        $group->refresh();
        $this->assertEquals('Another Name', $group->name);
        $this->assertNull($group->description);
    }

    public function testComponentValidatesGroup(): void
    {
        $group = $this->fakeGroupWithEventAndSiblingGroup();

        Livewire::test(EditGroup::class, ['group' => $group])
            ->set('form.name')
            ->call('save')
            ->assertHasErrors([
                'form.name' => 'required',
            ]);

        Livewire::test(EditGroup::class, ['group' => $group])
            ->set('form.name', 'Test Group 1')
            ->call('save')
            ->assertHasErrors([
                'form.name' => 'unique',
            ]);

        Livewire::test(EditGroup::class, ['group' => $group])
            ->set('form.description', \Illuminate\Support\Str::random(257))
            ->call('save')
            ->assertHasErrors([
                'form.description' => 'max:255',
            ]);
    }

    private function fakeGroupWithEventAndSiblingGroup(): Group
    {
        /** @var Event $event */
        $event = Event::factory(1)
            ->for(Location::factory()->create())
            ->create()
            ->first();
        $event->groups()->create([
            'name' => 'Test Group 1',
        ]);
        /** @var Group $group */
        return $event->groups()->create([
            'name' => 'Test Group 2',
        ]);
    }
}
