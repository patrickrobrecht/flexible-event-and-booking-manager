<?php

namespace Tests\Feature\Livewire\Groups;

use App\Enums\Ability;
use App\Enums\FormElementType;
use App\Livewire\Forms\GroupForm;
use App\Livewire\Groups\ManageGroups;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Event;
use App\Models\FormField;
use App\Models\Group;
use App\Policies\BookingPolicy;
use Illuminate\Database\Eloquent\Factories\Sequence;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\ActsAsUser;
use Tests\Traits\GeneratesTestData;

#[CoversClass(BookingPolicy::class)]
#[CoversClass(Group::class)]
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
                /** @phpstan-ignore argument.type */
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

        $bookingOptionListItemHtml = '<li class="list-group-item list-group-item-primary d-flex justify-content-between align-items-center avoid-break">';

        // Assert no booking date, but comment and email is shown for all visible bookings.
        foreach ($selectedBookingOptions as $bookingOption) {
            $testComponent->assertSeeHtml($bookingOptionListItemHtml . $bookingOption->name);
            $bookingOption->bookings->each(
                fn (Booking $booking) => $testComponent
                    ->assertSeeHtml('wire:key="booking' . $booking->id . '"')
                    /** @phpstan-ignore argument.type */
                    ->assertDontSeeText(formatDate($booking->booked_at))
                    ->assertSeeText($booking->comment)
                    ->assertSeeText($booking->email)
            );
        }

        // Assert booking of booking options not checked are actually not shown.
        foreach ($event->bookingOptions->whereNotIn('id', $selectedBookingOptionIds) as $bookingOption) {
            $testComponent->assertDontSeeHtml($bookingOptionListItemHtml . $bookingOption->name);
            $bookingOption->bookings->each(
                // Note: Only the booking's wire:key uniquely identifies the booking in the rendered HTML, names may contain duplicates.
                fn (Booking $booking) => $testComponent
                    ->assertDontSeeHtml('wire:key="booking' . $booking->id . '"')
                    ->assertDontSeeText($booking->comment)
                    ->assertDontSeeText($booking->email)
            );
        }
    }

    public function testGroupCreated(): void
    {
        $event = self::createEventWithBookings();
        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);

        self::assertCount(0, $event->groups);

        Livewire::test(ManageGroups::class, ['event' => $event])
            ->set('form.name', 'Test Group')
            ->set('form.description', 'Test Description')
            ->call('createGroup');

        self::assertCount(1, $event->refresh()->groups);
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
        self::assertCount(8, $event->groups);

        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);

        $group = $event->groups->random();
        Livewire::test(ManageGroups::class, ['event' => $event])
            ->call('deleteGroup', $group->id)
            ->assertSee(__('Group :name deleted successfully.', [
                'name' => $group->name,
            ]))
            ->assertDontSeeHtml('<h2 class="card-title">' . $group->name);

        self::assertCount(7, $event->refresh()->groups);
    }

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
        self::assertEquals($group->id, $booking->getGroup($event)?->id);

        $newGroup = $event->groups->except([$group->id])->random();

        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);
        Livewire::test(ManageGroups::class, ['event' => $event])
            ->call('moveBooking', $booking->id, $newGroup->id);

        $booking->refresh();
        self::assertEquals($newGroup->id, $booking->getGroup($event)?->id);
    }

    public function testBookingMovedWithoutGroup(): void
    {
        [$event, $booking] = $this->createEventWithBookingInGroup();

        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);
        Livewire::test(ManageGroups::class, ['event' => $event])
            ->call('moveBooking', $booking->id, -1);

        self::assertNull($booking->refresh()->getGroup($event));
    }

    public function testBookingMovedToSameGroupRemainsInGroup(): void
    {
        [$event, $booking] = $this->createEventWithBookingInGroup();
        $group = $booking->getGroup($event);
        self::assertNotNull($group);

        $pivotRow = (array) DB::table('booking_group')
            ->where('booking_id', $booking->id)
            ->sole();
        $pivotRowCount = DB::table('booking_group')->count();

        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);
        Livewire::test(ManageGroups::class, ['event' => $event])
            ->call('moveBooking', $booking->id, $group->id);

        // Assert the existing pivot row is kept unchanged (not deleted and re-created).
        $this->assertDatabaseHas('booking_group', $pivotRow);
        $this->assertDatabaseCount('booking_group', $pivotRowCount);
    }

    public function testBookingOfOtherEventNotMoved(): void
    {
        [$event, $booking] = $this->createEventWithBookingInGroup();
        $group = $booking->getGroup($event);
        self::assertNotNull($group);
        $bookingOfOtherEvent = self::createBooking();

        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);
        Livewire::test(ManageGroups::class, ['event' => $event])
            ->call('moveBooking', $bookingOfOtherEvent->id, $group->id);

        self::assertCount(0, $bookingOfOtherEvent->refresh()->groups);
    }

    public function testGroupUpdatedByEvent(): void
    {
        [$event, $booking] = $this->createEventWithBookingInGroup();
        $group = $booking->getGroup($event);
        self::assertNotNull($group);
        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);

        $testComponent = Livewire::test(ManageGroups::class, ['event' => $event]);

        $group->update(['name' => 'Renamed Group']);

        $testComponent
            ->dispatch('group-updated', group: $group->id)
            ->assertSee(__('Group :name updated successfully.', [
                'name' => 'Renamed Group',
            ]))
            ->assertSeeHtml('<h2 class="card-title">Renamed Group');
    }

    public function testBookingsSortedAndSortStoredInSession(): void
    {
        $bookingOption = self::createBookingOptionForEvent();
        $event = $bookingOption->event;
        foreach (['Anna', 'Berta', 'Clara', 'Doris'] as $lastName) {
            self::createBooking($bookingOption, ['last_name' => $lastName]);
        }
        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);

        $wireKeysInOrder = static fn (string $sort) => Booking::sort($event->bookings()->get(), $sort)
            ->map(fn (Booking $booking) => 'wire:key="booking' . $booking->id . '"')
            ->values()
            ->toArray();

        Livewire::test(ManageGroups::class, ['event' => $event])
            ->assertSeeHtmlInOrder($wireKeysInOrder('name'))
            ->set('sort', '-name')
            ->assertSet('sort', '-name')
            ->assertSeeHtmlInOrder($wireKeysInOrder('-name'));

        self::assertEquals('-name', Session::get('groups-settings-' . $event->id . '-sort'));
    }

    public function testBookingOptionIdsCastToInt(): void
    {
        $event = self::createEventWithBookingOptions(bookingOptionCount: 3);
        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);

        /** @var int[] $bookingOptionIds */
        $bookingOptionIds = $event->bookingOptions->take(2)->pluck('id')->toArray();

        Livewire::test(ManageGroups::class, ['event' => $event])
            ->set('bookingOptionIds', array_map('strval', $bookingOptionIds))
            ->assertSet('bookingOptionIds', $bookingOptionIds);

        self::assertEquals($bookingOptionIds, Session::get('groups-settings-' . $event->id . '-bookingOptionIds'));
    }

    public function testSelectedFormFieldsShown(): void
    {
        $bookingOption = self::createBookingOptionForEventWithCustomFormFields(formElementTypes: [FormElementType::Text]);
        $event = $bookingOption->event;
        $bookings = collect([
            self::createBooking($bookingOption),
            self::createBooking($bookingOption),
        ]);
        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);

        $customFormFields = $bookingOption->formFields->whereNull('column')->values();
        /** @var FormField $shownFormField */
        $shownFormField = $customFormFields[0];
        /** @var FormField $hiddenFormField */
        $hiddenFormField = $customFormFields[1];

        $testComponent = Livewire::test(ManageGroups::class, ['event' => $event])
            ->assertDontSeeText($shownFormField->name . ': ')
            ->set('showFields', [$shownFormField->id])
            ->assertDontSeeText($hiddenFormField->name . ': ');

        $bookings->each(
            fn (Booking $booking) => $testComponent
                ->assertSeeText($shownFormField->name . ': ' . $booking->getFieldValueAsText($shownFormField))
        );
    }

    /**
     * @return array{Event, Booking}
     */
    private function createEventWithBookingInGroup(): array
    {
        $event = self::createEventWithBookingOptions(bookingOptionCount: 2);
        self::createGroups($event, 2);

        /** @var Booking $booking */
        $booking = $event->getBookings()->random()->refresh();

        return [$event, $booking];
    }
}
