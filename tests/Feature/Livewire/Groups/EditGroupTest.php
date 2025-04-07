<?php

namespace Tests\Feature\Livewire\Groups;

use App\Livewire\Forms\GroupForm;
use App\Livewire\Groups\EditGroup;
use App\Models\Event;
use App\Models\Group;
use Illuminate\Support\Str;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\ActsAsUser;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Group::class)]
#[CoversClass(EditGroup::class)]
#[CoversClass(GroupForm::class)]
class EditGroupTest extends TestCase
{
    use ActsAsUser;
    use GeneratesTestData;

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
            ->for(self::createLocation())
            ->for(self::createOrganization())
            ->create()
            ->first();
        $event->groups()->create([
            'name' => 'Test Group 1',
        ]);
        /** @phpstan-ignore return.type */
        return $event->groups()->create([
            'name' => 'Test Group 2',
        ]);
    }
}
