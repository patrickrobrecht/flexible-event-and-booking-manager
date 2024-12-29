<?php

namespace App\Models;

use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\QueryBuilder\SortOptions;
use App\Models\Traits\BelongsToOrganization;
use App\Models\Traits\HasDocuments;
use App\Models\Traits\HasResponsibleUsers;
use App\Models\Traits\HasSlugForRouting;
use App\Options\Ability;
use App\Options\EventSeriesType;
use App\Options\FilterValue;
use App\Options\Visibility;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property Visibility $visibility
 *
 * @property-read Collection|Event[] $events {@see EventSeries::events()}
 * @property-read ?EventSeries $parentEventSeries {@see EventSeries::parentEventSeries()}
 * @property-read Collection|EventSeries[] subEventSeries {@see EventSeries::subEventSeries()}
 */
class EventSeries extends Model
{
    use BelongsToOrganization;
    use BuildsQueryFromRequest;
    use HasDocuments;
    use HasFactory;
    use HasResponsibleUsers;
    use HasSlugForRouting;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'events_count' => 'integer',
        'visibility' => Visibility::class,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'visibility',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'event_series_id')
            ->orderByDesc('started_at')
            ->orderByDesc('finished_at')
            ->orderBy('name');
    }

    public function parentEventSeries(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_event_series_id');
    }

    public function subEventSeries(): HasMany
    {
        return $this->hasMany(__CLASS__, 'parent_event_series_id')
            ->orderBy('name');
    }

    public function scopeEvent(Builder $query, int|string $eventId): Builder
    {
        return $this->scopeRelation($query, $eventId, 'events', fn (Builder $q) => $q->where('id', '=', $documentId));
    }

    public function scopeEventSeriesType(Builder $query, EventSeriesType|string $eventSeriesType): Builder
    {
        if (is_string($eventSeriesType)) {
            $eventSeriesType = EventSeriesType::tryFrom($eventSeriesType);
        }

        return match ($eventSeriesType) {
            EventSeriesType::MainEventSeries => $query->whereNull('parent_event_series_id'),
            EventSeriesType::PartOfEventSeries => $query->whereNotNull('parent_event_series_id'),
            EventSeriesType::EventSeriesWithParts => $query->whereHas('subEventSeries'),
            EventSeriesType::EventSeriesWithoutParts => $query->whereDoesntHave('subEventSeries'),
            default => $query,
        };
    }

    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);
        $this->organization()->associate($validatedData['organization_id']);
        $this->parentEventSeries()->associate($validatedData['parent_event_series_id'] ?? null);

        return $this->save()
            && $this->saveResponsibleUsers($validatedData);
    }

    public function getAbilityToViewResponsibilities(): Ability
    {
        return Ability::ViewResponsibilitiesOfEventSeries;
    }

    public function getRoute(): string
    {
        return route('event-series.show', $this);
    }

    public function getStoragePath(): string
    {
        return 'event-series/' . $this->id;
    }

    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('visibility')
                ->ignore(FilterValue::All->value),
            /** @see self::scopeEvent() */
            AllowedFilter::scope('event_id', 'event'),
            AllowedFilter::exact('organization_id'),
            /** @see self::scopeDocument() */
            AllowedFilter::scope('document_id', 'document'),
            /** @see self::scopeEventSeriesType() */
            AllowedFilter::scope('event_series_type')
                ->ignore(FilterValue::All->value)
                ->default(EventSeriesType::MainEventSeries->value),
        ];
    }

    public static function filterOptions(): array
    {
        return [
            FilterValue::All->value => __('all'),
            FilterValue::With->value => __('with any event series'),
            FilterValue::Without->value => __('without event series'),
        ];
    }

    public static function sortOptions(): SortOptions
    {
        return self::sortOptionsForNameAndTimeStamps();
    }
}
