<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\GroupGenerationMethod;
use App\Enums\Visibility;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Group;
use Closure;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class GroupControllerTest extends TestCase
{
    public function testUserCanViewGroupsOnlyWithCorrectAbility(): void
    {
        $event = self::createEventWithBookingOptions(Visibility::Private);

        $route = "/events/{$event->slug}/groups";
        $this->assertUserCanGetOnlyWithAbility($route, Ability::ViewBookingsOfEvent);

        // Verify content of the page.
        $response = $this->get($route)->assertOk();
        $event->bookings->each(fn (Booking $booking) => $response->assertSeeText($booking->bookedByUser->name ?? ''));
    }

    public function testUserCanExportGroupsOnlyWithCorrectAbility(): void
    {
        $parentEvent = self::createEventWithBookingOptions(Visibility::Private);
        self::createGroups($parentEvent, 3);

        $childEvent = self::createChildEvent(Visibility::Private, $parentEvent);
        self::assertTrue($parentEvent->is($childEvent->parentEvent));
        self::createGroups($childEvent, 4);

        $this->assertUserCanGetOnlyWithAbility("/events/{$parentEvent->slug}/groups?output=export", Ability::ExportGroupsOfEvent);
        $this->assertUserCanGetOnlyWithAbility("/events/{$childEvent->slug}/groups?output=export", Ability::ExportGroupsOfEvent);
    }

    public function testUserCannotViewGroupsOfEventWithoutBookingOptions(): void
    {
        $event = self::createEvent(Visibility::Private);
        $this->assertUserCannotGetDespiteAbility("/events/{$event->slug}/groups", Ability::ViewBookingsOfEvent);
    }

    #[DataProvider('groupGenerationMethods')]
    public function testUserCanGenerateGroupsWithCorrectAbility(GroupGenerationMethod $method): void
    {
        $event = self::createEventWithBookingOptions(Visibility::Private);

        $event->bookings->each(fn (Booking $booking) => self::assertNull($booking->getGroup($event)));
        self::assertCount(0, $event->groups);

        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);
        $formData = [
            'method' => $method->value,
            'groups_count' => 4,
            'booking_option_id' => $event->bookingOptions->pluck('id')->toArray(),
        ];
        $this->post("/events/{$event->slug}/groups/generate", $formData)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        $event->refresh()
            ->load('bookings.groups');
        self::assertCount(4, $event->groups);
        $event->bookings->each(fn (Booking $booking) => self::assertNotNull($booking->getGroup($event)));
    }

    /**
     * @return array<int, mixed[]>
     */
    public static function groupGenerationMethods(): array
    {
        return array_map(static fn (GroupGenerationMethod $method) => [$method], GroupGenerationMethod::cases());
    }

    public function testUserCanGenerateGroupsForChildEventExcludingParentGroupMembers(): void
    {
        $parentEvent = self::createEventWithBookingOptions(Visibility::Private, bookingOptionCount: 1);
        $bookings = $parentEvent->getBookings();
        self::assertGreaterThanOrEqual(2, $bookings->count());

        $excludedGroup = $parentEvent->findOrCreateGroup(1, 2);
        $excludedBooking = $bookings->first();
        self::assertNotNull($excludedBooking);
        $excludedBooking->groups()->attach($excludedGroup);

        $includedGroup = $parentEvent->findOrCreateGroup(2, 2);
        $includedBooking = $bookings->last();
        self::assertNotNull($includedBooking);
        $includedBooking->groups()->attach($includedGroup);

        $childEvent = self::createChildEvent(Visibility::Private, $parentEvent);

        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);
        $formData = [
            'method' => GroupGenerationMethod::Randomized->value,
            'groups_count' => 1,
            'booking_option_id' => $parentEvent->bookingOptions->pluck('id')->toArray(),
            'exclude_parent_group_id' => [$excludedGroup->id],
        ];
        $this->post("/events/{$childEvent->slug}/groups/generate", $formData)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        // The booking whose parent-event group was excluded must not be assigned to the newly generated group.
        self::assertNull($excludedBooking->refresh()->getGroup($childEvent));
        // Other bookings must still be assigned to the newly generated group.
        self::assertNotNull($includedBooking->refresh()->getGroup($childEvent));
    }

    public function testUserCanRegenerateGroupsDetachingPreviousGroupMembership(): void
    {
        $event = self::createEventWithBookingOptions(Visibility::Private, bookingOptionCount: 1);
        $bookings = $event->getBookings();
        self::assertGreaterThanOrEqual(1, $bookings->count());

        // Simulate a previous group generation run with more groups than generated in the second run.
        $staleGroup = $event->findOrCreateGroup(3);
        $bookings->each(fn (Booking $booking) => $booking->groups()->attach($staleGroup));

        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);
        $formData = [
            'method' => GroupGenerationMethod::Randomized->value,
            'groups_count' => 2,
            'booking_option_id' => $event->bookingOptions->pluck('id')->toArray(),
        ];
        $this->post("/events/{$event->slug}/groups/generate", $formData)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect();

        // The stale group must be empty again since every booking was moved to the newly generated group.
        self::assertCount(0, $staleGroup->refresh()->bookings);
        $bookings->each(function (Booking $booking) use ($event) {
            // Each booking must belong to exactly one group of this event, not both the stale and the new one.
            self::assertCount(1, $booking->refresh()->groups()->where('event_id', $event->id)->get());
        });
    }

    #[DataProvider('findOrCreateGroupTestCases')]
    public function testFindOrCreateGroupPadsNameForAlphabeticalSorting(
        int $groupIndex,
        int $groupsCount,
        string $expectedName
    ): void {
        $event = self::createEvent(Visibility::Private);

        $group = $event->findOrCreateGroup($groupIndex, $groupsCount);

        self::assertSame($expectedName, $group->name);
    }

    /**
     * @return array<string, array{int, int, string}>
     */
    public static function findOrCreateGroupTestCases(): array
    {
        return [
            'single-digit group count is not padded' => [3, 4, 'Gruppe 3'],
            'index is padded to the width of the largest index' => [9, 12, 'Gruppe 09'],
            'group count of exactly 100 requires three digits' => [1, 100, 'Gruppe 001'],
            'group count above 100 requires three digits' => [7, 101, 'Gruppe 007'],
        ];
    }

    #[DataProvider('deleteGroupTestCases')]
    public function testUserCanDeleteGroupsWithCorrectAbility(Closure $dataProvider, int $countAfterRequest): void
    {
        $event = self::createEventWithBookingOptions(Visibility::Private);
        self::createGroups($event, 3);
        self::assertCount(3, $event->groups);

        $this->actingAsUserWithAbility(Ability::ManageGroupsOfEvent);
        $this->delete("/events/{$event->slug}/groups", $dataProvider($event))->assertRedirect();

        $event->refresh();
        self::assertCount($countAfterRequest, $event->groups);
    }

    /**
     * @return array<int, array{Closure(Event): array<string, mixed>, int}>
     */
    public static function deleteGroupTestCases(): array
    {
        return [
            [fn (Event $event) => ['name' => $event->name], 0],
            [fn (Event $event) => ['name' => $event->name . ' '], 0],
            [fn (Event $event) => ['name' => Str::random(42)], 3],
            [fn (Event $event) => ['name' => ''], 3],
            [fn (Event $event) => [], 3],
        ];
    }
}
