<?php

namespace Tests\Feature\Livewire\Groups;

use App\Livewire\Forms\GroupForm;
use App\Livewire\Groups\ManageGroups;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use App\Models\Group;
use App\Models\Location;
use App\Options\Ability;
use Database\Factories\GroupFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\ActsAsUser;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Group::class)]
#[CoversClass(GroupFactory::class)]
#[CoversClass(GroupForm::class)]
#[CoversClass(ManageGroups::class)]
class ManageGroupsTest extends TestCase
{
    use ActsAsUser;
    use GeneratesTestData;

    public function testComponentRendersCorrectly(): void
    {
        Livewire::test(ManageGroups::class)
            ->assertStatus(200);
    }

    public function testGroupCreated(): void
    {
        $event = self::createEvent();
        $this->actingAsUserWithAbility([Ability::ManageGroupsOfEvent]);

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
            ->has(
                Group::factory()
                    ->sequence(fn (Sequence $sequence) => [
                        'name' => 'Group '. $sequence->index,
                    ])
                    ->count(8)
            )
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
    }#

    public function testBookingMoved(): void
    {
        $event = Event::factory()
            ->for(Location::factory())
            ->has(
                BookingOption::factory()
                    ->has(
                        Booking::factory()
                            ->count(2)
                    )
            )
            ->has(
                Group::factory()
                    ->sequence(fn (Sequence $sequence) => [
                        'name' => 'Group '. $sequence->index,
                    ])
                    ->count(2)
            )
            ->create();

        $group = $event->groups->random();
        /** @var Booking $booking */
        $booking = $event->bookings->random();
        $booking->groups()->attach($group);
        $this->assertEquals($group->id, $booking->getGroup($event)?->id);

        $newGroup = $event->groups->except($group->id)->random();

        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);
        Livewire::test(ManageGroups::class, ['event' => $event])
            ->call('moveBooking', $booking->id, $newGroup->id);

        $booking->refresh();
        $this->assertEquals($newGroup->id, $booking->getGroup($event)->id);
    }
}
