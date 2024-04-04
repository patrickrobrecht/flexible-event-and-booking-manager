<?php

namespace App\Models;

use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\QueryBuilder\SortOptions;
use App\Models\Traits\HasDocuments;
use App\Models\Traits\HasLocation;
use App\Models\Traits\HasSlugForRouting;
use App\Models\Traits\HasWebsite;
use App\Options\EventType;
use App\Options\Visibility;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\SortDirection;

/**
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property ?string $description
 * @property Visibility $visibility
 * @property ?Carbon $started_at
 * @property ?Carbon $finished_at
 *
 * @property-read Collection|BookingOption[] $bookingOptions {@see Event::bookingOptions()}
 * @property-read Collection|Booking[] $bookings {@see Event::bookings()}
 * @property-read ?EventSeries $eventSeries {@see Event::eventSeries()}
 * @property-read Collection|Group[] $groups {@see self::groups()}
 * @property-read Collection|Organization[] $organizations {@see Event::organizations()}
 * @property-read ?Event $parentEvent {@see Event::parentEvent()}
 * @property-read Collection|Event[] $subEvents {@see Event::subEvents()}
 */
class Event extends Model
{
    use BuildsQueryFromRequest;
    use HasDocuments;
    use HasFactory;
    use HasLocation;
    use HasSlugForRouting;
    use HasWebsite;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'visibility',
        'started_at',
        'finished_at',
        'website_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'groups_count' => 'integer',
        'visibility' => Visibility::class,
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    protected $perPage = 12;

    public function bookingOptions(): HasMany
    {
        return $this->hasMany(BookingOption::class, 'event_id');
    }

    public function bookings(): HasManyThrough
    {
        return $this->hasManyThrough(Booking::class, BookingOption::class);
    }

    public function groups(): HasMany
    {
        return $this->hasMany(Group::class, 'event_id')
            ->orderBy('name');
    }

    public function eventSeries(): BelongsTo
    {
        return $this->belongsTo(EventSeries::class, 'event_series_id');
    }

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->orderBy('name')
            ->withTimestamps();
    }

    public function parentEvent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_event_id');
    }

    public function subEvents(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_event_id')
            ->orderBy('started_at')
            ->orderBy('finished_at');
    }

    public function scopeDateFrom(Builder $query, Carbon|string $date): Builder
    {
        return $query
            ->where('started_at', '>=', Carbon::parse($date)->startOfDay()->format(self::getDateFormat()))
            ->orWhere(
                fn (Builder $startsBeforeAndEndsAfterSubQuery) => $startsBeforeAndEndsAfterSubQuery
                    ->where('started_at', '<=', Carbon::parse($date)->endOfDay()->format(self::getDateFormat()))
                    ->where('finished_at', '>=', Carbon::parse($date)->startOfDay()->format(self::getDateFormat()))
            );
    }

    public function scopeDateUntil(Builder $query, Carbon|string $date): Builder
    {
        return $query
            ->where('finished_at', '<=', Carbon::parse($date)->endOfDay()->format(self::getDateFormat()))
            ->orWhere(
                fn (Builder $startsBeforeButEndsAfter) => $startsBeforeButEndsAfter
                    ->where('started_at', '<=', Carbon::parse($date)->endOfDay()->format(self::getDateFormat()))
                    ->where('finished_at', '>=', Carbon::parse($date)->startOfDay()->format(self::getDateFormat()))
            );
    }

    public function scopeEventType(Builder $query, EventType|string $eventType): Builder
    {
        if (is_string($eventType)) {
            $eventType = EventType::tryFrom($eventType);
        }

        return match ($eventType) {
            EventType::MainEvent => $query->whereNull('parent_event_id'),
            EventType::PartOfEvent => $query->whereNotNull('parent_event_id'),
            EventType::EventWithParts => $query->whereHas('subEvents'),
            EventType::EventWithoutParts => $query->whereDoesntHave('subEvents'),
            default => $query,
        };
    }

    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);
        $this->location()->associate($validatedData['location_id'] ?? null);
        $this->eventSeries()->associate($validatedData['event_series_id'] ?? null);
        $this->parentEvent()->associate($validatedData['parent_event_id'] ?? null);

        return $this->save()
            && $this->organizations()->sync($validatedData['organization_id'] ?? []);
    }

    public function getBookingOptions(): Collection
    {
        if (isset($this->parentEvent)) {
            return $this->parentEvent->getBookingOptions();
        }

        $this->loadMissing([
            'bookingOptions' => fn (HasMany $bookingOptionsQuery) => $bookingOptionsQuery->withCount([
                'bookings',
            ]),
        ]);

        return $this->bookingOptions;
    }

    public function getBookings(): Collection
    {
        if (isset($this->parentEvent)) {
            return $this->parentEvent->getBookings();
        }

        $this->loadMissing([
            'bookings.bookingOption',
            'bookings.groups',
        ]);

        return $this->bookings;
    }

    public function getRoute(): string
    {
        return route('events.show', $this);
    }

    public function getStoragePath(): string
    {
        return 'events/' . $this->id;
    }

    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('visibility'),
            /** @see self::scopeDateFrom() */
            AllowedFilter::scope('date_from')
                ->default(Carbon::today()->format('Y-m-d')),
            /** @see self::scopeDateUntil() */
            AllowedFilter::scope('date_until'),
            AllowedFilter::exact('location_id'),
            AllowedFilter::exact('organization_id', 'organizations.id'),
            /** @see self::scopeEventType() */
            AllowedFilter::scope('event_type')
                ->default(EventType::MainEvent->value),
        ];
    }

    public static function sortOptions(): SortOptions
    {
        return (new SortOptions())
            ->addBothDirections(__('Name'), AllowedSort::field('name'))
            ->merge(self::sortOptionsForTimeStamps())
            ->addBothDirections(
                __('Period of the event'),
                AllowedSort::callback(
                    'period',
                    fn (Builder $query, bool $descending, string $property) => $query
                        ->orderBy('started_at', $descending ? SortDirection::DESCENDING : SortDirection::ASCENDING)
                        ->orderBy('finished_at', $descending ? SortDirection::DESCENDING : SortDirection::ASCENDING)
                ),
                true
            );
    }
}
