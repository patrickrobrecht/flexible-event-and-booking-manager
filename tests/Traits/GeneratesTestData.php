<?php

namespace Tests\Traits;

use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\Document;
use App\Models\DocumentReview;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Group;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use App\Options\FileType;
use App\Options\Visibility;
use Closure;
use Database\Factories\BookingFactory;
use Database\Factories\BookingOptionFactory;
use Database\Factories\EventFactory;
use Database\Factories\EventSeriesFactory;
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

    protected static function createBooking(): Booking
    {
        return Booking::factory()
            ->for(self::createBookingOptionForEvent(Visibility::Public))
            ->has(User::factory(), 'bookedByUser')
            ->create();
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

    protected static function createBookingOptionForEvent(Visibility $visibility): BookingOption
    {
        return BookingOption::factory()
            ->for(self::createEvent($visibility))
            ->create();
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

    protected static function createEvent(Visibility $visibility = null): Event
    {
        return Event::factory()
            ->visibility($visibility ?? null)
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

    protected static function createEventSeries(Visibility $visibility): EventSeries
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
