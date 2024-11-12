<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\BookingOptionController;
use App\Http\Requests\BookingOptionRequest;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\User;
use App\Options\Ability;
use App\Options\BookingRestriction;
use App\Options\Visibility;
use App\Policies\BookingOptionPolicy;
use Closure;
use Database\Factories\BookingOptionFactory;
use Database\Factories\UserFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(BookingOption::class)]
#[CoversClass(BookingOptionController::class)]
#[CoversClass(BookingOptionFactory::class)]
#[CoversClass(BookingOptionPolicy::class)]
#[CoversClass(BookingOptionRequest::class)]
#[CoversClass(BookingRestriction::class)]
class BookingOptionControllerTest extends TestCase
{
    use GeneratesTestData;
    use RefreshDatabase;

    public function testGuestCanViewBookingOptionOfPublicEvent(): void
    {
        $bookingOption = self::createBookingOptionForEvent(Visibility::Public);
        $this->assertGuestCanGet("/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}");
    }

    public function testGuestCannotViewBookingOptionOfPrivateEvent(): void
    {
        $bookingOption = self::createBookingOptionForEvent(Visibility::Private);
        $this->assertGuestCannotGet("/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}", false);
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

    public static function casesForBookingOptions(): array
    {
        return [
            [
                fn (BookingOptionFactory $factory) => $factory->availabilityStartingInFuture(),
                fn (BookingOption $bookingOption) => [
                    __('Bookings are not possible yet.'),
                    __('The booking period starts at :date.', ['date' => formatDateTime($bookingOption->available_from)]),
                ],
            ],
            [
                fn (BookingOptionFactory $factory) => $factory->availabilityEndedInPast(),
                fn (BookingOption $bookingOption) => [
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
            $this->actingAs($userFactory->create());
        }

        $this->get("events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}")
            ->assertOk()
            ->assertSeeText($errorMessageProvider($bookingOption));
    }

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
        $bookingOption = BookingOption::factory()->makeOne();
        $data = [
            ...$bookingOption->toArray(),
            'available_from' => $bookingOption->available_from->format('Y-m-d\TH:i'),
            'available_until' => $bookingOption->available_until->format('Y-m-d\TH:i'),
        ];

        $this->assertUserCanPostOnlyWithAbility("events/{$event->slug}/booking-options", $data, Ability::ManageBookingOptionsOfEvent, null);
    }

    #[DataProvider('visibilityProvider')]
    public function testUserCanOpenEditBookingOptionFormOnlyWithCorrectAbility(Visibility $visibility): void
    {
        $bookingOption = self::createBookingOptionForEvent($visibility);
        $this->assertUserCanGetOnlyWithAbility("/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}/edit", Ability::ManageBookingOptionsOfEvent);
    }
}
