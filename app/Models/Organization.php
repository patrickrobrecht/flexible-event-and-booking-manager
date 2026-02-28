<?php

namespace App\Models;

use App\Enums\Ability;
use App\Enums\ActiveStatus;
use App\Enums\FilterValue;
use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\QueryBuilder\SortOptions;
use App\Models\Traits\BelongsToLocation;
use App\Models\Traits\HasDocuments;
use App\Models\Traits\HasPhone;
use App\Models\Traits\HasResponsibleUsers;
use App\Models\Traits\HasSlugForRouting;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property ActiveStatus $status
 * @property ?string $register_entry
 * @property ?string $phone
 * @property ?string $email
 * @property ?string $website_url
 * @property-read int $location_id
 * @property ?string $bank_account_holder
 * @property ?string $iban
 * @property ?string $bank_name
 * @property-read string[] $bank_account_lines {@see self::bankAccountLines()}
 * @property Collection|Event[] $events {@see self::events()}
 * @property Collection|EventSeries[] $eventSeries {@see self::eventSeries()}
 * @property Collection|Material[] $materials {@see self::materials()}
 */
class Organization extends Model
{
    use BelongsToLocation;
    use BuildsQueryFromRequest;
    use HasDocuments;
    use HasFactory;
    use HasPhone;
    use HasResponsibleUsers;
    use HasSlugForRouting;

    protected $fillable = [
        'name',
        'slug',
        'status',
        'register_entry',
        'phone',
        'email',
        'website_url',
        'bank_account_holder',
        'iban',
        'bank_name',
    ];

    protected $casts = [
        'status' => ActiveStatus::class,
    ];

    public function bankAccountLines(): Attribute
    {
        return Attribute::get(fn () => isset($this->iban, $this->bank_name) ? [
            __('Account holder') . ': ' . ($this->bank_account_holder ?? $this->name),
            'IBAN: ' . $this->iban,
            __('Bank') . ': ' .$this->bank_name,
        ] : [])->shouldCache();
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function eventSeries(): HasMany
    {
        return $this->hasMany(EventSeries::class);
    }

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class);
    }

    public function scopeEvent(Builder $query, int|string $eventId): Builder
    {
        return $this->scopeRelation($query, $eventId, 'events', fn (Builder $q) => $q->where('event_id', '=', $eventId));
    }

    /**
     * @param array{location_id: int} $validatedData
     */
    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);

        $this->location()->associate($validatedData['location_id']);

        if (!$this->save()) {
            return false;
        }

        $this->saveResponsibleUsers($validatedData);
        return true;
    }

    public function getAbilityToViewResponsibilities(): Ability
    {
        return Ability::ViewResponsibilitiesOfOrganizations;
    }

    public function getRoute(): string
    {
        return route('organizations.show', $this);
    }

    public function getRouteForGallery(): string
    {
        return route('organizations.gallery', $this);
    }

    public function getStoragePath(): string
    {
        return 'organizations/' . $this->id;
    }

    /**
     * @return AllowedFilter[]
     */
    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            /** @see self::scopeEvent() */
            AllowedFilter::scope('event_id', 'event'),
            AllowedFilter::exact('location_id')
                ->ignore(FilterValue::All->value),
            /** @see self::scopeDocument() */
            AllowedFilter::scope('document_id', 'document'),
            AllowedFilter::exact('status')
                ->default(ActiveStatus::Active->value)
                ->ignore(FilterValue::All->value),
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function filterOptions(): array
    {
        return [
            FilterValue::All->value => __('all'),
            FilterValue::With->value => __('with any organization'),
            FilterValue::Without->value => __('without organizations'),
        ];
    }

    public static function sortOptions(): SortOptions
    {
        return self::sortOptionsForNameAndTimeStamps()
            ->addBothDirections(__('Number of events'), self::allowedSortForRelationCount('events'))
            ->addBothDirections(__('Number of event series'), self::allowedSortForRelationCount('eventSeries'));
    }
}
