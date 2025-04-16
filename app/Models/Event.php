<?php

namespace App\Models;

use App\Enums\Ability;
use App\Enums\EventType;
use App\Enums\FilterValue;
use App\Enums\Visibility;
use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\QueryBuilder\SortOptions;
use App\Models\Traits\BelongsToLocation;
use App\Models\Traits\BelongsToOrganization;
use App\Models\Traits\FiltersByRelationExistence;
use App\Models\Traits\HasDocuments;
use App\Models\Traits\HasNameAndDescription;
use App\Models\Traits\HasResponsibleUsers;
use App\Models\Traits\HasSlugForRouting;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
 * @property ?string $website_url
 *
 * @property-read Collection|BookingOption[] $bookingOptions {@see Event::bookingOptions()}
 * @property-read Collection|Booking[] $bookings {@see Event::bookings()}
 * @property-read ?EventSeries $eventSeries {@see Event::eventSeries()}
 * @property-read Collection|Group[] $groups {@see self::groups()}
 * @property-read ?Event $parentEvent {@see Event::parentEvent()}
 * @property-read Collection|Event[] $subEvents {@see Event::subEvents()}
 */
class Event extends Model
{
    use BelongsToLocation;
    use BelongsToOrganization;
    use BuildsQueryFromRequest;
    use FiltersByRelationExistence;
    use HasDocuments;
    use HasFactory;
    use HasNameAndDescription;
    use HasResponsibleUsers;
    use HasSlugForRouting;

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
        'parent_event_id' => 'integer',
        'event_series_id' => 'integer',
        'organization_id' => 'integer',
    ];

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

    public function scopeEventSeries(Builder $query, int|string $eventSeriesId): Builder
    {
        return $this->scopeRelation($query, $eventSeriesId, 'eventSeries', fn (Builder $q) => $q->where('id', '=', $eventSeriesId));
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

    public function deleteWithGroups(): bool
    {
        $this->groups()->delete();
        return $this->delete();
    }

    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);
        $this->location()->associate($validatedData['location_id'] ?? null);
        $this->eventSeries()->associate($validatedData['event_series_id'] ?? null);
        $this->parentEvent()->associate($validatedData['parent_event_id'] ?? null);
        $this->organization()->associate($validatedData['organization_id'] ?? null);

        return $this->save()
            && $this->saveResponsibleUsers($validatedData);
    }

    public function findOrCreateGroup(int|string $groupIndex): Group
    {
        return $this->groups()
            ->firstOrCreate([
                'name' => __('Group') . ' ' . $groupIndex,
            ]);
    }

    public function getAbilityToViewResponsibilities(): Ability
    {
        return Ability::ViewResponsibilitiesOfEvents;
    }

    /**
     * @return Collection<BookingOption>
     */
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

    /**
     * @return Collection<Booking>
     */
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
            /** @see self::scopeSearchNameAndDescription() */
            AllowedFilter::scope('search', 'searchNameAndDescription'),
            AllowedFilter::exact('visibility')
                ->ignore(FilterValue::All->value),
            /** @see self::scopeDateFrom() */
            AllowedFilter::scope('date_from'),
            /** @see self::scopeDateUntil() */
            AllowedFilter::scope('date_until'),
            /** @see self::scopeEventSeries() */
            AllowedFilter::scope('event_series_id', 'eventSeries'),
            AllowedFilter::exact('organization_id')
                ->ignore(FilterValue::All->value),
            AllowedFilter::exact('location_id')
                ->ignore(FilterValue::All->value),
            /** @see self::scopeDocument() */
            AllowedFilter::scope('document_id', 'document'),
            /** @see self::scopeEventType() */
            AllowedFilter::scope('event_type')
                ->default(EventType::MainEvent->value)
                ->ignore(FilterValue::All->value),
        ];
    }

    public static function filterOptions(): array
    {
        return [
            FilterValue::All->value => __('all'),
            FilterValue::With->value => __('with at least one event'),
            FilterValue::Without->value => __('without events'),
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
