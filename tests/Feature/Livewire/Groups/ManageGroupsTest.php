<?php

namespace Livewire\Groups;

use App\Livewire\Groups\ManageGroups;
use App\Models\Event;
use App\Models\Group;
use App\Models\Location;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Livewire\Livewire;
use Tests\TestCase;
use Tests\Traits\ActsAsUser;

class ManageGroupsTest extends TestCase
{
    use ActsAsUser;

    public function testComponentRendersCorrectly(): void
    {
        Livewire::test(ManageGroups::class)
            ->assertStatus(200);
    }

    public function testGroupCreated(): void
    {
        $event = Event::factory()
            ->for(Location::factory())
            ->create();

        $this->actingAsAdmin();

        $this->assertCount(0, $event->groups);

        Livewire::test(ManageGroups::class, ['event' => $event])
            ->set('form.name', 'Test Group')
            ->set('form.description', 'Test Description')
            ->call('createGroup');

        $this->assertCount(1, $event->refresh()->groups);
    }

    public function testGroupDeleted(): void
    {
        $event = Event::factory()
            ->for(Location::factory())
            ->has(Group::factory()
                ->sequence(fn (Sequence $sequence) => [
                    'name' => 'Group '. $sequence->index,
                ])
                ->count(8))
            ->create();
        $this->assertCount(8, $event->groups);

        $this->actingAsAdmin();

        $group = $event->groups->random();
        Livewire::test(ManageGroups::class, ['event' => $event])
            ->call('deleteGroup', $group->id)
            ->assertSee(__('Group :name deleted successfully.', [
                'name' => $group->name,
            ]))
            ->assertDontSeeHtml('<h2 class="card-title">' . $group->name);

        $this->assertCount(7, $event->refresh()->groups);
    }
}
