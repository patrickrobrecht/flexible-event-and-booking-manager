<?php

namespace Tests\Traits;

use App\Enums\FileType;
use App\Enums\FormElementType;
use App\Enums\MaterialStatus;
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
use App\Models\Material;
use App\Models\Organization;
use App\Models\StorageLocation;
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
use Illuminate\Database\Eloquent\Model;
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
    /**
     * @template TModel of Model
     *
     * @param Factory<TModel> $factory
     * @return Collection<int, TModel>
     */
    public function createCollection(Factory $factory, ?int $count = null): Collection
    {
        /** @phpstan-ignore return.type */
        return $factory
            ->count($count ?? $this->faker->numberBetween(5, 10))
            ->create();
    }

    /**
     * @return array<int, mixed[]>
     */
    public static function visibilityProvider(): array
    {
        return array_map(static fn (Visibility $visibility) => [$visibility], Visibility::cases());
    }

    /**
     * @param array<string, mixed> $attributes
     */
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
                    $filePath = $bookingOption->getFilePath() . '/' . fake()->uuid() . '.txt';
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

    /**
     * @return Collection<int, Booking>
     */
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

    /**
     * @return Collection<int, Booking>
     */
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

    /**
     * @param array<string, mixed> $attributes
     */
    protected static function createBookingOptionForEvent(?Visibility $visibility = null, array $attributes = []): BookingOption
    {
        return BookingOption::factory()
            ->for(self::createEvent($visibility))
            ->create($attributes);
    }

    /**
     * @param FormElementType[]|null $formElementTypes
     */
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
            ->forReference($referenceProvider())
            ->for(User::factory()->create(), 'uploadedByUser')
            ->create();
    }

    /**
     * @return Collection<int, Document>
     */
    protected static function createDocuments(): Collection
    {
        return Document::factory()
            /** @phpstan-ignore-next-line argument.type */
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

    protected static function createMaterial(?int $storageLocationCount = null): Material
    {
        return Material::factory()
            ->forOrganization(self::createOrganization())
            ->hasStorageLocations($storageLocationCount)
            ->create();
    }

    protected static function createOrganization(): Organization
    {
        return Organization::factory()
            ->for(self::createLocation())
            ->withBankAccount()
            ->create();
    }

    protected static function createStorageLocation(
        ?StorageLocation $parentStorageLocation = null,
        int $childStorageLocationsCount = 0,
        int $materialsCount = 0,
    ): StorageLocation {
        return StorageLocation::factory()
            ->has(
                StorageLocation::factory()->count($childStorageLocationsCount),
                'childStorageLocations'
            )
            ->hasAttached(
                Material::factory()->forOrganization()->count($materialsCount),
                ['material_status' => MaterialStatus::Checked],
                'materials'
            )
            ->forParentStorageLocation($parentStorageLocation)
            ->create();
    }

    public static function createUserResponsibleFor(Event|EventSeries|Organization $responsibleFor): User
    {
        return User::factory()
            /** @phpstan-ignore-next-line match.unhandled */
            ->hasAttached($responsibleFor, ['publicly_visible' => true], match ($responsibleFor::class) {
                Event::class => 'responsibleForEvents',
                EventSeries::class => 'responsibleForEventSeries',
                Organization::class => 'responsibleForOrganizations',
            })
            ->create();
    }

    /**
     * @return Collection<int, User>
     */
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
     * @phpstan-ignore missingType.generics
     */
    public static function makeData(Factory $factory): array
    {
        return $factory->makeOne()->toArray();
    }

    /**
     * @param string[] $without
     * @return array<string, mixed>
     * @phpstan-ignore missingType.generics
     */
    public static function makeDataWithout(Factory $factory, array $without = []): array
    {
        return Arr::except(self::makeData($factory), $without);
    }
}
