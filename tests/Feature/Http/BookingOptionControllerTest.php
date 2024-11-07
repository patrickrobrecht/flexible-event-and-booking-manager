<?php

namespace Http;

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

    public function testBookingOptionOfPublicEventIsAccessibleByEveryone(): void
    {
        $bookingOption = self::createBookingOptionForEvent(Visibility::Public);
        $this->assertRouteAccessibleAsGuest("/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}");
    }

    public function testBookingOptionOfPrivateEventIsNotAccessibleAsGuest(): void
    {
        $bookingOption = self::createBookingOptionForEvent(Visibility::Private);
        $this->get("/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}")->assertForbidden();
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

    #[DataProvider('visibilityProvider')]
    public function testCreateEventFormIsOnlyAccessibleWithCorrectAbility(Visibility $visibility): void
    {
        $event = self::createEvent($visibility);
        $this->assertRouteOnlyAccessibleWithAbility("/events/{$event->slug}/booking-options/create", Ability::ManageBookingOptionsOfEvent);
    }

    #[DataProvider('visibilityProvider')]
    public function testEditEventFormIsAccessibleOnlyWithCorrectAbility(Visibility $visibility): void
    {
        $bookingOption = self::createBookingOptionForEvent($visibility);
        $this->assertRouteOnlyAccessibleWithAbility("/events/{$bookingOption->event->slug}/booking-options/{$bookingOption->slug}/edit", Ability::ManageBookingOptionsOfEvent);
    }
}
