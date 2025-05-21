<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Enums\BookingRestriction;
use App\Enums\Visibility;
use App\Http\Controllers\BookingOptionController;
use App\Http\Requests\BookingOptionRequest;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\User;
use App\Policies\BookingOptionPolicy;
use Closure;
use Database\Factories\BookingOptionFactory;
use Database\Factories\UserFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(BookingOption::class)]
#[CoversClass(BookingOptionController::class)]
#[CoversClass(BookingOptionPolicy::class)]
#[CoversClass(BookingOptionRequest::class)]
#[CoversClass(BookingRestriction::class)]
class BookingOptionControllerTest extends TestCase
{
    public function testGuestCanViewBookingOptionOfPublicEvent(): void
    {
        $bookingOption = self::createBookingOptionForEvent(Visibility::Public);
        $this->assertGuestCanGet("/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}");
    }

    public function testGuestCanViewBookingOptionOfPublicEventWithCustomFields(): void
    {
        $bookingOption = self::createBookingOptionForEventWithCustomFormFields(Visibility::Public);
        $this->assertGuestCanGet("/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}");
    }

    public function testGuestCannotViewBookingOptionOfPrivateEvent(): void
    {
        $bookingOption = self::createBookingOptionForEvent(Visibility::Private);
        $this->assertGuestCannotGet(
            "/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}",
            false
        );
    }

    public function testUserCanViewBookingOptionOfPrivateEventWithCorrectAbility(): void
    {
        $bookingOption = self::createBookingOptionForEvent(Visibility::Private);
        $this->actingAsUserWithAbility(Ability::ViewPrivateEvents);
        $this->get("/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}")->assertOk();
    }

    #[DataProvider('casesForBookingOptions')]
    public function testBookingOptionShowsHintOnAvailability(Closure $bookingOptionProvider, Closure $errorMessageProvider): void
    {
        $bookingOption =
            $bookingOptionProvider(
                BookingOption::factory()
                    ->for(self::createEvent(Visibility::Public))
            )
            ->create();

        $this->get("events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}")
            ->assertOk()
            ->assertSeeText($errorMessageProvider($bookingOption));
    }

    /**
     * @return array<int, array{Closure(BookingOptionFactory): BookingOptionFactory, Closure(BookingOption): string[]}>
     */
    public static function casesForBookingOptions(): array
    {
        return [
            [
                fn (BookingOptionFactory $factory) => $factory->availabilityStartingInFuture(),
                fn (BookingOption $bookingOption) => [
                    __('Bookings are not possible yet.'),
                    /** @phpstan-ignore-next-line argument.type */
                    __('The booking period starts at :date.', ['date' => formatDateTime($bookingOption->available_from)]),
                ],
            ],
            [
                fn (BookingOptionFactory $factory) => $factory->availabilityEndedInPast(),
                fn (BookingOption $bookingOption) => [
                    /** @phpstan-ignore-next-line argument.type */
                    __('The booking period ended at :date.', ['date' => formatDateTime($bookingOption->available_until)]),
                    __('Bookings are not possible anymore.'),
                ],
            ],
            [
                fn (BookingOptionFactory $factory) => $factory->maximumBookings(5)->has(Booking::factory()->count(5)),
                fn (BookingOption $bookingOption) => __('The maximum number of bookings has been reached.'),
            ],
        ];
    }

    #[DataProvider('bookingRestrictions')]
    public function testBookingOptionShowsRestriction(BookingRestriction $restriction, ?UserFactory $userFactory, Closure $errorMessageProvider): void
    {
        $bookingOption = BookingOption::factory()
            ->for(self::createEvent(Visibility::Public))
            ->restriction($restriction)
            ->create();

        if (isset($userFactory)) {
            /** @var User $user */
            $user = $userFactory->create();
            $this->actingAs($user);
        }

        $this->get("events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}")
            ->assertOk()
            ->assertSeeText($errorMessageProvider($bookingOption));
    }

    /**
     * @return array<int, array{BookingRestriction, ?UserFactory, Closure(): string}>
     */
    public static function bookingRestrictions(): array
    {
        return [
            [
                BookingRestriction::AccountRequired,
                null,
                fn () => __('Bookings are only available for logged-in users.'),
            ],
            [
                BookingRestriction::VerifiedEmailAddressRequired,
                User::factory()->unverified(),
                fn () => __('Bookings are only available for logged-in users with a verified email address.'),
            ],
        ];
    }

    public function testUserCanOpenCreateBookingOptionFormOnlyWithCorrectAbility(): void
    {
        $event = self::createEvent();
        $this->assertUserCanGetOnlyWithAbility("/events/{$event->slug}/booking-options/create", Ability::ManageBookingOptionsOfEvent);
    }

    public function testUserCanStoreBookingOptionOnlyWithCorrectAbility(): void
    {
        $event = self::createEvent();
        $data = $this->generateRandomBookingOptionData();

        $this->assertUserCanPostOnlyWithAbility("events/{$event->slug}/booking-options", $data, Ability::ManageBookingOptionsOfEvent, null);
    }

    public function testUserCanOpenEditBookingOptionFormOnlyWithCorrectAbility(): void
    {
        $bookingOption = self::createBookingOptionForEvent();
        $this->assertUserCanGetOnlyWithAbility("/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}/edit", Ability::ManageBookingOptionsOfEvent);
    }

    public function testUserCanUpdateBookingOptionOnlyWithCorrectAbility(): void
    {
        $bookingOption = self::createBookingOptionForEvent();
        /** @var array{slug: string} $data */
        $data = $this->generateRandomBookingOptionData();

        $this->assertUserCanPutOnlyWithAbility(
            "/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}",
            $data,
            Ability::ManageBookingOptionsOfEvent,
            "/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}/edit",
            "/events/{$bookingOption->event->slug}"
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function generateRandomBookingOptionData(): array
    {
        $bookingOption = BookingOption::factory()->makeOne();
        return [
            ...$bookingOption->toArray(),
            /** @phpstan-ignore method.nonObject */
            'available_from' => $bookingOption->available_from->format('Y-m-d\TH:i'),
            /** @phpstan-ignore method.nonObject */
            'available_until' => $bookingOption->available_until->format('Y-m-d\TH:i'),
        ];
    }
}
