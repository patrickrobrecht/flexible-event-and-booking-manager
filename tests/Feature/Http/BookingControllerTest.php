<?php

namespace Http;

use App\Events\BookingCompleted;
use App\Exports\BookingsExportSpreadsheet;
use App\Http\Controllers\BookingController;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\Filters\BookingFilterRequest;
use App\Listeners\SendBookingConfirmation;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\User;
use App\Notifications\BookingConfirmation;
use App\Options\Ability;
use App\Options\DeletedFilter;
use App\Options\FilterValue;
use App\Options\PaymentStatus;
use App\Options\Visibility;
use App\Policies\BookingPolicy;
use Database\Factories\BookingFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Notification;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Booking::class)]
#[CoversClass(BookingConfirmation::class)]
#[CoversClass(BookingCompleted::class)]
#[CoversClass(BookingController::class)]
#[CoversClass(BookingFactory::class)]
#[CoversClass(BookingFilterRequest::class)]
#[CoversClass(BookingPolicy::class)]
#[CoversClass(BookingRequest::class)]
#[CoversClass(BookingsExportSpreadsheet::class)]
#[CoversClass(DeletedFilter::class)]
#[CoversClass(FilterValue::class)]
#[CoversClass(PaymentStatus::class)]
#[CoversClass(SendBookingConfirmation::class)]
class BookingControllerTest extends TestCase
{
    use GeneratesTestData;
    use RefreshDatabase;

    #[DataProvider('visibilityProvider')]
    public function testBookingsOfEventCanBeListedWithCorrectAbility(Visibility $visibility): void
    {
        $bookingOption = self::createBookingOptionForEvent($visibility);
        $users = self::createUsersWithBookings($bookingOption);

        $route = "/events/{$bookingOption->event->slug}/{$bookingOption->slug}/bookings";
        $this->assertUserCanGetOnlyWithAbility($route, Ability::ViewBookingsOfEvent);

        // Verify content of the page.
        $response = $this->get($route)->assertOk();
        $users->each(fn (User $user) => $response->assertSeeText($user->name));
    }

    #[DataProvider('visibilityProvider')]
    public function testBookingsOfEventCanBeExportedWithCorrectAbility(Visibility $visibility): void
    {
        $bookingOption = self::createBookingOptionForEvent($visibility);
        self::createUsersWithBookings($bookingOption);

        $route = "/events/{$bookingOption->event->slug}/{$bookingOption->slug}/bookings?output=export";
        $this->assertUserCanGetOnlyWithAbility($route, Ability::ExportBookingsOfEvent);
    }

    #[DataProvider('visibilityProvider')]
    public function testSingleBookingAreAccessibleWithCorrectAbility(Visibility $visibility): void
    {
        $this->actingAsUserWithAbility(Ability::ViewBookingsOfEvent);

        $bookingOption = self::createBookingOptionForEvent($visibility);
        self::createUsersWithBookings($bookingOption)
            ->each(fn (User $user) => $user->bookings->each(function (Booking $booking) {
                $this->assertUserCanGetOnlyWithAbility("bookings/{$booking->id}", Ability::ViewBookingsOfEvent);
                $this->assertUserCanGetOnlyWithAbility("bookings/{$booking->id}/pdf", Ability::ViewBookingsOfEvent);
            }));
    }

    #[DataProvider('visibilityProvider')]
    public function testUserCanAccessOwnBookingsWithoutAbility(Visibility $visibility): void
    {
        $bookingOption = self::createBookingOptionForEvent($visibility);
        $user = $this->actingAsAnyUser();

        self::createBookingsForUser($bookingOption, $user)
            ->each(fn (Booking $booking) => $this->get("bookings/{$booking->id}")->assertOk());
    }

    public function testBookingIsStoredAndConfirmationSent(): void
    {
        $user = $this->actingAsAnyUser();

        Notification::fake();

        $bookingOption = self::createBookingOptionForEvent(Visibility::Public);
        $this->assertCount(0, $user->bookings);
        $this->assertCount(0, $bookingOption->bookings);
        $booking = Booking::factory()
            ->withoutDateOfBirth()
            ->makeOne();
        $this->post("events/{$bookingOption->event->slug}/{$bookingOption->slug}/bookings", $booking->toArray())
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect();
        $this->assertCount(1, $user->refresh()->bookings);
        $this->assertCount(1, $bookingOption->refresh()->bookings);

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            BookingConfirmation::class,
            static function ($notification, $channels, $notifiable) use ($user, $booking) {
                $mailContent = $notification->toMail(new AnonymousNotifiable())->render();
                return $notifiable->routes['mail'] === $user->email
                    && str_contains($mailContent, $booking->first_name)
                    && str_contains($mailContent, $booking->price . ' €');
            }
        );
    }

    public function testGuestBookingIsStoredAndConfirmationSent(): void
    {
        Notification::fake();

        $bookingOption = self::createBookingOptionForEvent(Visibility::Public);
        $booking = Booking::factory()
            ->withoutDateOfBirth()
            ->makeOne();
        $this->post("events/{$bookingOption->event->slug}/{$bookingOption->slug}/bookings", $booking->toArray())
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect();
        $this->assertCount(1, $bookingOption->refresh()->bookings);

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            BookingConfirmation::class,
            static function ($notification, $channels, $notifiable) use ($booking) {
                return $notifiable->routes['mail'] === $booking->email;
            }
        );
    }

    public function testSendBookingConfirmationListenerSendsNotification(): void
    {
        Notification::fake();
        $booking = self::createBooking();

        $listener = new SendBookingConfirmation();
        $listener->handle(new BookingCompleted($booking));

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            BookingConfirmation::class,
            static function ($notification, $channels, $notifiable) use ($booking) {
                return $notifiable->routes['mail'] === $booking->email;
            }
        );
    }

    public function testBookingCannotBeCreatedBeforeAvailabilityPeriod(): void
    {
        $user = $this->actingAsAnyUser();

        $bookingOption = BookingOption::factory()
            ->for(self::createEvent(Visibility::Public))
            ->availabilityStartingInFuture()
            ->create();

        $this->post("events/{$bookingOption->event->slug}/{$bookingOption->slug}/bookings", $user->toArray())
            ->assertForbidden()
            ->assertSeeText(__('Bookings are not possible yet.'));
    }

    #[DataProvider('visibilityProvider')]
    public function testEditBookingIsAccessibleOnlyWithCorrectAbility(Visibility $visibility): void
    {
        $this->actingAsUserWithAbility(Ability::ViewBookingsOfEvent);

        $bookingOption = self::createBookingOptionForEvent($visibility);
        self::createUsersWithBookings($bookingOption)
            ->each(fn (User $user) => $user->bookings->each(function (Booking $booking) {
                $this->assertUserCanGetOnlyWithAbility("bookings/{$booking->id}/edit", Ability::EditBookingsOfEvent);
            }));
    }

    #[DataProvider('visibilityProvider')]
    public function testABookingCanBeDeletedAndRestoredOnlyWithCorrectAbility(Visibility $visibility): void
    {
        $user = $this->actingAsUserWithAbility(Ability::DeleteAndRestoreBookingsOfEvent);

        $bookingOption = self::createBookingOptionForEvent($visibility);
        self::createBookingsForUser($bookingOption, User::factory()->create())
            ->each(function (Booking $booking) use ($user) {
                $this->assertUserCan($user, 'delete', $booking);
                $this->assertUserCannot($user, 'restore', $booking);

                $this->delete("bookings/{$booking->id}")->assertRedirect();
                $this->assertSoftDeleted($booking);

                $booking->refresh();
                $this->assertUserCannot($user, 'delete', $booking);
                $this->assertUserCan($user, 'restore', $booking);

                $this->patch("bookings/{$booking->id}/restore")->assertRedirect();
                $this->assertNotSoftDeleted($booking);
            });
    }
}
