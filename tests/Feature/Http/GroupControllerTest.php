<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\GroupGenerationMethod;
use App\Enums\Visibility;
use App\Exports\GroupsExportSpreadsheet;
use App\GroupGenerationMethods\AgeBasedGroupGenerationMethod;
use App\GroupGenerationMethods\GeneralGroupGenerationMethod;
use App\GroupGenerationMethods\RandomizedAgeBasedGroupGenerationMethod;
use App\GroupGenerationMethods\RandomizedGroupGenerationMethod;
use App\Http\Controllers\GroupController;
use App\Http\Requests\Filters\GroupFilterRequest;
use App\Http\Requests\GenerateGroupsRequest;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Group;
use App\Policies\GroupPolicy;
use Closure;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(AgeBasedGroupGenerationMethod::class)]
#[CoversClass(Event::class)]
#[CoversClass(GeneralGroupGenerationMethod::class)]
#[CoversClass(GenerateGroupsRequest::class)]
#[CoversClass(Group::class)]
#[CoversClass(GroupController::class)]
#[CoversClass(GroupFilterRequest::class)]
#[CoversClass(GroupPolicy::class)]
#[CoversClass(GroupGenerationMethod::class)]
#[CoversClass(GroupsExportSpreadsheet::class)]
#[CoversClass(RandomizedAgeBasedGroupGenerationMethod::class)]
#[CoversClass(RandomizedGroupGenerationMethod::class)]
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
