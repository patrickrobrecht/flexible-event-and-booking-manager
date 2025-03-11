<?php

namespace Tests\Feature\Http;

use App\Events\BookingCompleted;
use App\Exports\BookingsExportSpreadsheet;
use App\Http\Controllers\BookingController;
use App\Http\Requests\BookingPaymentRequest;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\Filters\BookingFilterRequest;
use App\Listeners\SendBookingConfirmation;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\FormField;
use App\Models\FormFieldValue;
use App\Models\User;
use App\Notifications\BookingConfirmation;
use App\Options\Ability;
use App\Options\DeletedFilter;
use App\Options\FilterValue;
use App\Options\FormElementType;
use App\Options\PaymentStatus;
use App\Options\Visibility;
use App\Policies\BookingPolicy;
use Database\Factories\BookingFactory;
use Database\Factories\FormFieldFactory;
use Database\Factories\FormFieldValueFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Tests\Traits\GeneratesTestData;

#[CoversClass(Booking::class)]
#[CoversClass(BookingConfirmation::class)]
#[CoversClass(BookingCompleted::class)]
#[CoversClass(BookingController::class)]
#[CoversClass(BookingFactory::class)]
#[CoversClass(BookingFilterRequest::class)]
#[CoversClass(BookingPaymentRequest::class)]
#[CoversClass(BookingPolicy::class)]
#[CoversClass(BookingRequest::class)]
#[CoversClass(BookingsExportSpreadsheet::class)]
#[CoversClass(DeletedFilter::class)]
#[CoversClass(FormElementType::class)]
#[CoversClass(FormField::class)]
#[CoversClass(FormFieldFactory::class)]
#[CoversClass(FormFieldValue::class)]
#[CoversClass(FormFieldValueFactory::class)]
#[CoversClass(FilterValue::class)]
#[CoversClass(PaymentStatus::class)]
#[CoversClass(SendBookingConfirmation::class)]
class BookingControllerTest extends TestCase
{
    use GeneratesTestData;
    use RefreshDatabase;

    public function testUserCanViewBookingsOfEventOnlyWithCorrectAbility(): void
    {
        $bookingOption = self::createBookingOptionForEvent();
        $users = self::createUsersWithBookings($bookingOption);

        $route = "/events/{$bookingOption->event->slug}/{$bookingOption->slug}/bookings";
        $this->assertUserCanGetOnlyWithAbility($route, Ability::ViewBookingsOfEvent);
        $this->assertUserCanGetOnlyWithAbility($route . '?output=pdf', Ability::ViewBookingsOfEvent);

        // Verify content of the page.
        $response = $this->get($route)->assertOk();
        $users->each(fn (User $user) => $response->assertSeeText($user->name));

        // Cleanup generated PDFs.
        $this->assertTrue(Storage::disk('local')->deleteDirectory($bookingOption->getFilePath()));
    }

    public function testUserCanExportBookingsOfEventOnlyWithCorrectAbility(): void
    {
        $bookingOption = self::createBookingOptionForEvent();
        self::createUsersWithBookings($bookingOption);

        $route = "/events/{$bookingOption->event->slug}/{$bookingOption->slug}/bookings?output=export";
        $this->assertUserCanGetOnlyWithAbility($route, Ability::ExportBookingsOfEvent);
    }

    public function testUserCanExportBookingsOfEventWithCustomFormOnlyWithCorrectAbility(): void
    {
        $bookingOption = self::createBookingOptionForEventWithCustomFormFields();
        Collection::times($this->faker->numberBetween(3, 20), static fn () => self::createBooking($bookingOption));

        $route = "/events/{$bookingOption->event->slug}/{$bookingOption->slug}/bookings?output=export";
        $this->assertUserCanGetOnlyWithAbility($route, Ability::ExportBookingsOfEvent);
    }

    public function testUserCanDownloadUploadedFilesForBookingsOnlyWithCorrectAbility(): void
    {
        $this->actingAsUserWithAbility(Ability::ViewBookingsOfEvent);

        $bookingOption = self::createBookingOptionForEventWithCustomFormFields()->refresh();
        for ($i = 1; $i <= $this->faker->numberBetween(3, 20); $i++) {
            self::createBooking($bookingOption);
        }

        foreach ($bookingOption->formFieldsForFiles as $formFieldForFile) {
            $route = "/events/{$bookingOption->event->slug}/{$bookingOption->slug}/bookings?output={$formFieldForFile->id}";
            $this->assertUserCanGetOnlyWithAbility($route, Ability::ViewBookingsOfEvent);
        }

        // Cleanup generated files.
        $this->assertTrue(Storage::disk('local')->deleteDirectory($bookingOption->getFilePath()));
    }

    public function testUserCanViewSingleBookingOnlyWithCorrectAbility(): void
    {
        $this->actingAsUserWithAbility(Ability::ViewBookingsOfEvent);

        $bookingOption = self::createBookingOptionForEvent();
        self::createUsersWithBookings($bookingOption)
            ->each(fn (User $user) => $user->bookings->each(function (Booking $booking) {
                $this->assertUserCanGetOnlyWithAbility("bookings/{$booking->id}", Ability::ViewBookingsOfEvent);
                $this->assertUserCanGetOnlyWithAbility("bookings/{$booking->id}/pdf", Ability::ViewBookingsOfEvent);
            }));

        // Cleanup generated PDFs.
        $this->assertTrue(Storage::disk('local')->deleteDirectory($bookingOption->getFilePath()));
    }

    public function testUserCanViewUploadedFileForBookingOnlyWithCorrectAbility(): void
    {
        $this->actingAsUserWithAbility(Ability::ViewBookingsOfEvent);

        $bookingOption = self::createBookingOptionForEventWithCustomFormFields()->refresh();
        $booking = self::createBooking($bookingOption)->refresh();

        $this->assertUserCanGetOnlyWithAbility("bookings/{$booking->id}", Ability::ViewBookingsOfEvent);
        $this->assertUserCanGetOnlyWithAbility("bookings/{$booking->id}/pdf", Ability::ViewBookingsOfEvent);
        foreach ($bookingOption->formFieldsForFiles as $formFieldForFile) {
            $formFieldValue = $booking->formFieldValues->firstWhere('form_field_id', $formFieldForFile->id);
            $this->assertUserCanGetOnlyWithAbility("bookings/{$booking->id}/file/{$formFieldValue->id}", Ability::ViewBookingsOfEvent);
        }

        // Cleanup generated files.
        $this->assertTrue(Storage::disk('local')->deleteDirectory($bookingOption->getFilePath()));
    }

    public function testUserCanViewPaymentsOnlyWithCorrectAbility(): void
    {
        $bookingOption = self::createBookingOptionForEvent();
        $bookings = self::createBookings($bookingOption);

        $route = "/events/{$bookingOption->event->slug}/{$bookingOption->slug}/payments";
        $this->assertUserCanGetOnlyWithAbility($route, Ability::ViewPaymentStatus);

        // Verify content of the page.
        $response = $this->get($route)->assertOk();
        $bookings->each(fn (Booking $booking) => $response->assertSeeText($booking->first_name)->assertSeeText($booking->last_name));
    }

    public function testUserCanViewOwnBookingsWithoutAbility(): void
    {
        $bookingOption = self::createBookingOptionForEvent();
        $user = $this->actingAsAnyUser();

        self::createBookingsForUser($bookingOption, $user)
            ->each(fn (Booking $booking) => $this->get("bookings/{$booking->id}")->assertOk());
    }

    public function testUserCanSubmitBookingAndReceivesConfirmationViaMail(): void
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
        $this->assertCount(1, $bookingOption->event->bookings);

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            BookingConfirmation::class,
            static function ($notification, $channels, $notifiable) use ($booking, $bookingOption) {
                $mailContent = $notification->toMail(new AnonymousNotifiable())->render();
                return $notifiable->routes['mail'] === $booking->email
                    && str_contains($mailContent, $booking->first_name)
                    && str_contains($mailContent, formatDecimal($bookingOption->price) . ' €')
                    && str_contains($mailContent, $bookingOption->event->organization->iban);
            }
        );
    }

    public function testGuestCanSubmitBookingAndReceivesConfirmationViaMail(): void
    {
        $this->assertGuestCanSubmitBookingAndReceivesConfirmationViaMail(
            self::createBookingOptionForEvent(Visibility::Public)
        );
    }

    public function testGuestCanSubmitBookingWithCustomFormAndReceivesConfirmationViaMail(): void
    {
        $this->assertGuestCanSubmitBookingAndReceivesConfirmationViaMail(
            self::createBookingOptionForEventWithCustomFormFields(Visibility::Public, [FormElementType::Date, FormElementType::Text])
        );
    }

    private function assertGuestCanSubmitBookingAndReceivesConfirmationViaMail(BookingOption $bookingOption): void
    {
        Notification::fake();

        $data = $this->generateRandomBookingData($bookingOption);
        $this->post("events/{$bookingOption->event->slug}/{$bookingOption->slug}/bookings", $data)
            ->assertSessionDoesntHaveErrors()
            ->assertRedirect();
        $this->assertCount(1, $bookingOption->refresh()->bookings);
        $this->assertCount(1, $bookingOption->event->bookings);

        Notification::assertSentTo(
            new AnonymousNotifiable(),
            BookingConfirmation::class,
            static function ($notification, $channels, $notifiable) use ($data) {
                return $notifiable->routes['mail'] === $data['email'];
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

    public function testUserCannotCreateBookingBeforeAvailabilityPeriod(): void
    {
        $user = $this->actingAsAnyUser();

        $bookingOption = BookingOption::factory()
            ->for(self::createEvent())
            ->availabilityStartingInFuture()
            ->create();

        $this->post("events/{$bookingOption->event->slug}/{$bookingOption->slug}/bookings", $user->toArray())
            ->assertForbidden()
            ->assertSeeText(__('Bookings are not possible yet.'));
    }

    public function testUserCanOpenEditBookingFormOnlyWithCorrectAbility(): void
    {
        $this->actingAsUserWithAbility(Ability::ViewBookingsOfEvent);

        $bookingOption = self::createBookingOptionForEvent();
        self::createUsersWithBookings($bookingOption)
            ->each(fn (User $user) => $user->bookings->each(function (Booking $booking) {
                $this->assertUserCanGetOnlyWithAbility("bookings/{$booking->id}/edit", Ability::EditBookingsOfEvent);
            }));
    }

    public function testUserCanOpenEditBookingFormWithCustomFieldsOnlyWithCorrectAbility(): void
    {
        $this->actingAsUserWithAbility(Ability::ViewBookingsOfEvent);

        $bookingOption = self::createBookingOptionForEventWithCustomFormFields();
        $booking = self::createBooking($bookingOption);
        $this->assertUserCanGetOnlyWithAbility("bookings/{$booking->id}/edit", Ability::EditBookingsOfEvent);
    }

    public function testUserCanUpdateBookingOnlyWithCorrectAbility(): void
    {
        $booking = self::createBooking();

        $editRoute = "/bookings/{$booking->id}/edit";
        $data = $this->generateRandomBookingData($booking->bookingOption);
        $this->assertUserCanPutOnlyWithAbility("/bookings/{$booking->id}", $data, Ability::EditBookingsOfEvent, $editRoute, $editRoute);
        $this->assertEquals($data['first_name'], $booking->refresh()->first_name);
    }

    public function testUserCanUpdatePaymentsOnlyWithCorrectAbility(): void
    {
        $bookingOption = self::createBookingOptionForEvent();
        $bookings = self::createBookings($bookingOption);
        $bookings2 = self::createBookings($bookingOption);

        $route = "/events/{$bookingOption->event->slug}/{$bookingOption->slug}/payments";
        $data = [
            'booking_id' => $bookings->pluck('id')->toArray(),
            'paid_at' => $this->faker->dateTime()->format('Y-m-d\TH:i'),
        ];
        $this->assertUserCanPutOnlyWithAbility($route, $data, Ability::EditPaymentStatus, $route, $route);

        $this->assertEquals($bookings->count(), $bookingOption->bookings()->whereNotNull('paid_at')->count());
        $this->assertEquals($bookings2->count(), $bookingOption->bookings()->whereNull('paid_at')->count());
    }

    public function testUserCannotUpdatePaymentOfPaidBookings(): void
    {
        $bookingOption = self::createBookingOptionForEvent();
        $bookings = self::createBookings($bookingOption);
        /** @var Booking $booking */
        $booking = $bookings->first();
        $booking->paid_at = $this->faker->dateTime();
        $booking->save();

        $route = "/events/{$bookingOption->event->slug}/{$bookingOption->slug}/payments";
        $data = [
            'booking_id' => [$booking->id],
            'paid_at' => $this->faker->dateTime()->format('Y-m-d\TH:i'),
        ];
        $this->actingAsUserWithAbility(Ability::EditPaymentStatus);
        $this->put($route, $data)
            ->assertSessionHasErrors(['booking_id']);
    }

    public function testUserCanDeleteAndRestoreBookingOnlyWithCorrectAbility(): void
    {
        $user = $this->actingAsUserWithAbility(Ability::DeleteAndRestoreBookingsOfEvent);

        $bookingOption = self::createBookingOptionForEvent();
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

    private function generateRandomBookingData(BookingOption $bookingOption): array
    {
        if ($bookingOption->formFields->isEmpty()) {
            $data = Booking::factory()
                ->withoutDateOfBirth()
                ->makeOne()
                ->toArray();
            unset($data['comment']);
            return $data;
        }

        $data = [];

        foreach ($bookingOption->formFields as $formField) {
            $data[$formField->input_name] = FormFieldValue::factory()
                ->forFormField($formField)
                ->makeOne()
                ->value;
        }

        return $data;
    }
}
