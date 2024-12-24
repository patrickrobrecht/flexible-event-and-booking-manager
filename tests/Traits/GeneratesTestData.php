<?php

namespace Tests\Traits;

use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Document;
use App\Models\DocumentReview;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\FormField;
use App\Models\FormFieldValue;
use App\Models\Group;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use App\Options\FileType;
use App\Options\FormElementType;
use App\Options\Visibility;
use Closure;
use Database\Factories\BookingFactory;
use Database\Factories\BookingOptionFactory;
use Database\Factories\EventFactory;
use Database\Factories\EventSeriesFactory;
use Database\Factories\FormFieldFactory;
use Database\Factories\FormFieldValueFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Booking::class)]
#[CoversClass(BookingFactory::class)]
#[CoversClass(BookingOption::class)]
#[CoversClass(BookingOptionFactory::class)]
#[CoversClass(Event::class)]
#[CoversClass(EventFactory::class)]
#[CoversClass(EventSeries::class)]
#[CoversClass(EventSeriesFactory::class)]
#[CoversClass(FormField::class)]
#[CoversClass(FormFieldFactory::class)]
#[CoversClass(FormFieldValue::class)]
#[CoversClass(FormFieldValueFactory::class)]
#[CoversClass(Group::class)]
#[CoversClass(User::class)]
#[CoversClass(UserFactory::class)]
#[CoversClass(Visibility::class)]
trait GeneratesTestData
{
    public static function visibilityProvider(): array
    {
        return array_map(static fn (Visibility $method) => [$method], Visibility::cases());
    }

    protected static function createBooking(?BookingOption $bookingOption = null): Booking
    {
        $booking = Booking::factory()
            ->for($bookingOption ?? self::createBookingOptionForEvent(Visibility::Public))
            ->has(User::factory(), 'bookedByUser')
            ->create();

        if (isset($bookingOption) && $bookingOption->formFields->isNotEmpty()) {
            foreach ($bookingOption->formFields as $formField) {
                $booking->setFieldValue(
                    $formField,
                    FormFieldValue::factory()
                        ->forFormField($formField)
                        ->makeOne()
                        ->value
                );
            }
        }

        return $booking;
    }

    protected static function createBookings(BookingOption $bookingOption): Collection
    {
        return Booking::factory()
            ->for($bookingOption)
            ->has(User::factory(), 'bookedByUser')
            ->count(fake()->numberBetween(5, 42))
            ->create();
    }

    protected static function createBookingsForUser(BookingOption $bookingOption, User $user): Collection
    {
        return Booking::factory()
            ->for($bookingOption)
            ->for($user, 'bookedByUser')
            ->count(fake()->numberBetween(1, 3))
            ->create();
    }

    protected static function createBookingOptionForEvent(?Visibility $visibility = null): BookingOption
    {
        return BookingOption::factory()
            ->for(self::createEvent($visibility))
            ->create();
    }

    protected static function createBookingOptionForEventWithCustomFormFields(?Visibility $visibility = null): BookingOption
    {
        $bookingOptionFactory = BookingOption::factory()
            ->for(self::createEvent($visibility))
            ->has(FormField::factory()->forColumn('first_name'))
            ->has(FormField::factory()->forColumn('last_name'))
            ->has(FormField::factory()->forColumn('email'));

        foreach (FormElementType::casesForFields() as $type) {
            $bookingOptionFactory
                ->has(
                    FormField::factory()
                        ->count(random_int(2, 10))
                        ->forType($type)
                        ->sequence(
                            ['required' => true],
                            ['required' => false]
                        )
                );
        }

        return $bookingOptionFactory->create();
    }

    protected static function createChildEvent(Visibility $visibility, Event $event): Event
    {
        return Event::factory()
            ->for($event, 'parentEvent')
            ->for($event->location)
            ->visibility($visibility)
            ->create();
    }

    protected static function createDocument(Closure $referenceProvider): Document
    {
        return Document::factory()
            ->for($referenceProvider(), 'reference')
            ->for(User::factory()->create(), 'uploadedByUser')
            ->create();
    }

    protected static function createDocuments(): Collection
    {
        return Document::factory()
            ->for(fake()->randomElement([
                self::createEvent(Visibility::Public),
                self::createEventSeries(Visibility::Private),
                self::createOrganization(),
            ]), 'reference')
            ->for(User::factory()->create(), 'uploadedByUser')
            ->count(fake()->numberBetween(3, 5))
            ->create()
            ->each(function (Document $document) {
                $document->file_type = FileType::PDF;
                $document->path = $document->reference->getDocumentStoragePath() . '/' . $document->id . '.pdf';
                $document->save();
            });
    }

    protected static function createDocumentWithReview(Closure $referenceProvider, User $user): DocumentReview
    {
        return DocumentReview::factory()
            ->for(self::createDocument($referenceProvider))
            ->for($user)
            ->create();
    }

    protected static function createEvent(?Visibility $visibility = null): Event
    {
        return Event::factory()
            ->visibility($visibility)
            ->for(Location::factory()->create())
            ->create();
    }

    protected static function createEventWithBookingOptions(Visibility $visibility): Event
    {
        $event = Event::factory()
            ->visibility($visibility)
            ->for(Location::factory()->create())
            ->has(
                BookingOption::factory()
                    ->count(fake()->numberBetween(3, 5))
            )
            ->create();

        $event->bookingOptions->each(fn (BookingOption $bookingOption) => self::createBookings($bookingOption));

        return $event;
    }

    protected static function createEventSeries(?Visibility $visibility = null): EventSeries
    {
        return EventSeries::factory()
            ->has(
                Event::factory()
                    ->for(Location::factory()->create())
                    ->count(fake()->numberBetween(1, 5))
            )
            ->visibility($visibility)
            ->create();
    }

    protected static function createGroups(Event $event, int $count): void
    {
        $groups = [];
        foreach (range(1, $count) as $groupIndex) {
            $group = $event->findOrCreateGroup($groupIndex);
            $groups[] = $group->id;
        }

        foreach ($event->getBookings() as $booking) {
            $booking->groups()->attach(fake()->randomElement($groups));
        }
    }

    protected static function createOrganization(): Organization
    {
        return Organization::factory()
            ->for(Location::factory()->create())
            ->create();
    }

    protected static function createUsersWithBookings(BookingOption $bookingOption): Collection
    {
        return User::factory()
            ->count(fake()->numberBetween(2, 5))
            ->create()
            ->each(fn ($user) => self::createBookingsForUser($bookingOption, $user));
    }
}
