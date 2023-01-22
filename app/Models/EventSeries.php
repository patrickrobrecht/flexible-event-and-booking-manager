<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Models\Traits\HasSlugForRouting;
use App\Options\Visibility;
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
    use Filterable;
    use HasFactory;
    use HasSlugForRouting;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
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

    protected $perPage = 12;

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'event_series_id')
            ->orderBy('started_at')
            ->orderBy('finished_at');
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

    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);
        $this->parentEventSeries()->associate($validatedData['parent_event_series_id'] ?? null);

        return $this->save();
    }

    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
        ];
    }
}
