<?php

namespace Tests\Feature\Livewire\Groups;

use App\Enums\Ability;
use App\Livewire\Forms\GroupForm;
use App\Livewire\Groups\ManageGroups;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use App\Models\Group;
use App\Policies\BookingPolicy;
use Database\Factories\GroupFactory;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\ActsAsUser;
use Tests\Traits\GeneratesTestData;

#[CoversClass(BookingPolicy::class)]
#[CoversClass(Group::class)]
#[CoversClass(GroupFactory::class)]
#[CoversClass(GroupForm::class)]
#[CoversClass(ManageGroups::class)]
class ManageGroupsTest extends TestCase
{
    use ActsAsUser;
    use GeneratesTestData;

    public function testComponentRendersWithDefaultSettings(): void
    {
        $event = self::createEventWithBookingOptions();
        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);

        $testComponent = Livewire::test(ManageGroups::class, ['event' => $event])
            ->assertOk()
            ->assertSet('sort', 'name')
            ->assertSet('bookingOptionIds', $event->bookingOptions->pluck('id')->toArray())
            ->assertSet('showBookingData', ['booked_at'])
            ->assertDontSeeText(__('Payment status'))
            ->assertDontSeeText(__('Comment'));

        // Assert Booking date is shown for all bookings.
        $event->bookings->each(
            fn (Booking $booking) => $testComponent
                ->assertSeeHtml($booking->first_name . ' <strong>' . $booking->last_name)
                ->assertSeeText(formatDate($booking->booked_at))
        );
    }

    public function testComponentTakesSettingsFromSession(): void
    {
        $event = self::createEventWithBookings();
        $this->actingAsUserWithAbility([Ability::ManageGroupsOfEvent, Ability::ViewPaymentStatus, Ability::EditBookingComment]);

        Session::put('groups-settings-' . $event->id . '-sort', 'date_of_birth');
        $selectedBookingOptions = $event->bookingOptions->random(2);
        $selectedBookingOptionIds = $selectedBookingOptions->pluck('id')->toArray();
        Session::put('groups-settings-' . $event->id . '-bookingOptionIds', $selectedBookingOptionIds);
        Session::put('groups-settings-' . $event->id . '-showBookingData', ['comment', 'email']);

        $testComponent = Livewire::test(ManageGroups::class, ['event' => $event])
            ->assertOk()
            ->assertSet('sort', 'date_of_birth')
            ->assertSet('bookingOptionIds', $selectedBookingOptionIds)
            ->assertSet('showBookingData', ['comment', 'email'])
            ->assertSeeText(__('Payment status'))
            ->assertSeeText(__('Comment'));

        $bookingOptionListItemHtml = '<li class="list-group-item list-group-item-primary d-flex justify-content-between align-items-center">';

        // Assert no booking date, but comment and email is shown for all visible bookings.
        foreach ($selectedBookingOptions as $bookingOption) {
            $testComponent->assertSeeHtml($bookingOptionListItemHtml . $bookingOption->name);
            $bookingOption->bookings->each(
                fn (Booking $booking) => $testComponent
                    ->assertSeeHtml($booking->first_name . ' <strong>' . $booking->last_name)
                    ->assertDontSeeText(formatDate($booking->booked_at))
                    ->assertSeeText($booking->comment)
                    ->assertSeeText($booking->email)
            );
        }

        // Assert booking of booking options not checked are actually not shown.
        foreach ($event->bookingOptions->whereNotIn('id', $selectedBookingOptionIds) as $bookingOption) {
            $testComponent->assertDontSeeHtml($bookingOptionListItemHtml . $bookingOption->name);
            $bookingOption->bookings->each(
                fn (Booking $booking) => $testComponent
                    ->assertDontSeeHtml($booking->first_name . ' <strong>' . $booking->last_name)
                    ->assertDontSeeText($booking->comment)
                    ->assertDontSeeText($booking->email)
            );
        }
    }

    public function testGroupCreated(): void
    {
        $event = self::createEventWithBookings();
        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);

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
            ->for(self::createLocation())
            ->for(self::createOrganization())
            ->has(
                Group::factory()
                    ->sequence(fn (Sequence $sequence) => [
                        'name' => 'Group '. $sequence->index,
                    ])
                    ->count(8)
            )
            ->create();
        $this->assertCount(8, $event->groups);

        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);

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
            ->for(self::createLocation())
            ->for(self::createOrganization())
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
