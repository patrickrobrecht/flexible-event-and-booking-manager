<?php

namespace Tests\Feature\Livewire\Groups;

use App\Livewire\Groups\EditGroup;
use App\Models\Event;
use App\Models\Group;
use App\Models\Location;
use Illuminate\Support\Str;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\ActsAsUser;

class EditGroupTest extends TestCase
{
    use ActsAsUser;

    public function testComponentRendersCorrectly(): void
    {
        Livewire::test(EditGroup::class)
            ->assertStatus(200);
    }

    public function testGroupUpdated(): void
    {
        $group = $this->fakeGroupWithEventAndSiblingGroup();
        $this->assertNull($group->description);

        $this->actingAsAdmin();

        Livewire::test(EditGroup::class, ['group' => $group])
            ->set('form.description', 'Test Description');

        $group->refresh();
        $this->assertEquals('Test Description', $group->description);

        Livewire::test(EditGroup::class, ['group' => $group])
            ->set('form.name', 'Another Name')
            ->set('form.description');
        $group->refresh();
        $this->assertEquals('Another Name', $group->name);
        $this->assertNull($group->description);
    }

    public function testGroupValidated(): void
    {
        $group = $this->fakeGroupWithEventAndSiblingGroup();

        $this->actingAsAdmin();

        Livewire::test(EditGroup::class, ['group' => $group])
            ->set('form.name')
            ->assertHasErrors([
                'form.name' => 'required',
            ]);

        Livewire::test(EditGroup::class, ['group' => $group])
            ->set('form.name', 'Test Group 1')
            ->assertHasErrors([
                'form.name' => 'unique',
            ]);

        Livewire::test(EditGroup::class, ['group' => $group])
            ->set('form.description', Str::random(257))
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
