<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Models\Traits\HasLocation;
use App\Models\Traits\HasWebsite;
use App\Options\Visibility;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

/**
 * @property-read int $id
 * @property string $name
 * @property string $slug
 * @property string $description
 * @property Visibility $visibility
 * @property ?Carbon $started_at
 * @property ?Carbon $finished_at
 *
 * @property Collection|Organization[] $organizations {@see Event::organizations()}
 */
class Event extends Model
{
    use Filterable;
    use HasFactory;
    use HasLocation;
    use HasSlug;
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
        'visibility' => Visibility::class,
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function organizations(): BelongsToMany
    {
        return $this->belongsToMany(Organization::class)
            ->withTimestamps();
    }

    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);
        $this->location()->associate($validatedData['location_id'] ?? null);

        return $this->save()
            && $this->organizations()->sync($validatedData['organization_id'] ?? []);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function resolveRouteBinding($value, $field = null): ?static
    {
        /** @var ?Event $event */
        $event = self::query()
            ->where('slug', '=', $value)
            ->firstOrFail();

        return $event;
    }

    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('location_id'),
            AllowedFilter::exact('organization_id', 'organizations.id'),
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug')
            ->preventOverwrite();
    }
}
