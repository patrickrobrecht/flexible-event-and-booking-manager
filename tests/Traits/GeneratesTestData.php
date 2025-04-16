<?php

namespace Tests\Traits;

use App\Enums\FileType;
use App\Enums\FormElementType;
use App\Enums\Visibility;
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
use App\Models\UserRole;
use Closure;
use Database\Factories\BookingFactory;
use Database\Factories\BookingOptionFactory;
use Database\Factories\EventFactory;
use Database\Factories\EventSeriesFactory;
use Database\Factories\FormFieldFactory;
use Database\Factories\FormFieldValueFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
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
    public function createCollection(Factory $factory, ?int $count = null): Collection
    {
        return $factory
            ->count($count ?? $this->faker->numberBetween(5, 10))
            ->create();
    }

    public static function visibilityProvider(): array
    {
        return array_map(static fn (Visibility $method) => [$method], Visibility::cases());
    }

    protected static function createBooking(?BookingOption $bookingOption = null, array $attributes = []): Booking
    {
        $booking = Booking::factory()
            ->for($bookingOption ?? self::createBookingOptionForEvent(Visibility::Public))
            ->for(User::factory(), 'bookedByUser')
            ->create([
                ...$attributes,
                'price' => $bookingOption?->price,
            ]);

        if (isset($bookingOption) && $bookingOption->formFields->isNotEmpty()) {
            foreach ($bookingOption->formFields as $formField) {
                if ($formField->type === FormElementType::File) {
                    $filePath = $bookingOption->getFilePath() . '/' . fake()->uuid(). '.txt';
                    Storage::disk('local')->put($filePath, 'Test File Contents');
                    $value = $filePath;
                } else {
                    $value = FormFieldValue::factory()
                        ->forFormField($formField)
                        ->makeOne()
                        ->value;
                }

                $booking->setFieldValue($formField, $value);
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
            ->create([
                'price' => $bookingOption->price,
            ]);
    }

    protected static function createBookingsForUser(BookingOption $bookingOption, User $user): Collection
    {
        return Booking::factory()
            ->for($bookingOption)
            ->for($user, 'bookedByUser')
            ->count(fake()->numberBetween(1, 3))
            ->create([
                'price' => $bookingOption->price,
            ]);
    }

    protected static function createBookingOptionForEvent(?Visibility $visibility = null, array $attributes = []): BookingOption
    {
        return BookingOption::factory()
            ->for(self::createEvent($visibility))
            ->create($attributes);
    }

    protected static function createBookingOptionForEventWithCustomFormFields(?Visibility $visibility = null, ?array $formElementTypes = null): BookingOption
    {
        $bookingOptionFactory = BookingOption::factory()
            ->for(self::createEvent($visibility))
            ->has(FormField::factory()->forColumn('first_name'))
            ->has(FormField::factory()->forColumn('last_name'))
            ->has(FormField::factory()->forColumn('email'));

        foreach ($formElementTypes ?? FormElementType::casesForFields() as $type) {
            $bookingOptionFactory = $bookingOptionFactory
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
            ->for($event->organization)
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

    protected static function createEvent(?Visibility $visibility = null, int $subEventsCount = 0): Event
    {
        $organization = self::createOrganization();
        return Event::factory()
            ->visibility($visibility)
            ->for(self::createLocation())
            ->for($organization)
            ->has(
                Event::factory()
                    ->for(self::createLocation())
                    ->for($organization)
                    ->count($subEventsCount),
                'subEvents'
            )
            ->create();
    }

    protected static function createEventWithBookingOptions(?Visibility $visibility = null, ?int $bookingOptionCount = null): Event
    {
        $event = Event::factory()
            ->visibility($visibility)
            ->for(self::createLocation())
            ->for(self::createOrganization())
            ->has(
                BookingOption::factory()
                    ->count($bookingOptionCount ?? fake()->numberBetween(3, 5))
            )
            ->create();

        $event->bookingOptions->each(fn (BookingOption $bookingOption) => self::createBookings($bookingOption));

        return $event;
    }

    protected static function createEventWithBookings(?Visibility $visibility = null): Event
    {
        $event = self::createEventWithBookingOptions();

        foreach ($event->bookingOptions as $bookingOption) {
            self::createBookings($bookingOption);
        }

        return $event;
    }

    protected static function createEventSeries(?Visibility $visibility = null, ?int $eventsCount = null, int $subEventSeriesCount = 0): EventSeries
    {
        $organization = self::createOrganization();
        return EventSeries::factory()
            ->for($organization)
            ->has(
                Event::factory()
                    ->for(self::createLocation())
                    ->for($organization)
                    ->count($eventsCount ?? fake()->numberBetween(1, 5))
            )
            ->has(
                EventSeries::factory()
                    ->for($organization)
                    ->count($subEventSeriesCount),
                'subEventSeries'
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

    protected static function createLocation(): Location
    {
        return Location::factory()->create();
    }

    protected static function createOrganization(): Organization
    {
        return Organization::factory()
            ->for(self::createLocation())
            ->withBankAccount()
            ->create();
    }

    public static function createUserResponsibleFor(Event|EventSeries|Organization $responsibleFor): User
    {
        return User::factory()
            ->hasAttached($responsibleFor, ['publicly_visible' => true], match ($responsibleFor::class) {
                Event::class => 'responsibleForEvents',
                EventSeries::class => 'responsibleForEventSeries',
                Organization::class => 'responsibleForOrganizations',
            })
            ->create();
    }

    protected static function createUsersWithBookings(BookingOption $bookingOption): Collection
    {
        return User::factory()
            ->count(fake()->numberBetween(2, 5))
            ->create()
            ->each(fn ($user) => self::createBookingsForUser($bookingOption, $user));
    }

    public static function createUserWithUserRole(UserRole $userRole): User
    {
        return User::factory()
            ->hasAttached($userRole)
            ->create();
    }

    /**
     * @return array<string, mixed>
     */
    public static function makeData(Factory $factory): array
    {
        return $factory->makeOne()->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    public static function makeDataWithout(Factory $factory, array $without = []): array
    {
        return Arr::except(self::makeData($factory), $without);
    }
}
