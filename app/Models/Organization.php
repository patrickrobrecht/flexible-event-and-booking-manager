<?php

namespace App\Models;

use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\QueryBuilder\SortOptions;
use App\Models\Traits\HasDocuments;
use App\Models\Traits\HasLocation;
use App\Models\Traits\HasResponsibleUsers;
use App\Models\Traits\HasWebsite;
use App\Options\Ability;
use App\Options\ActiveStatus;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property-read int $id
 * @property string $name
 * @property ActiveStatus $status
 * @property ?string $register_entry
 * @property ?string $representatives
 *
 * @property Collection|Event[] $events {@see Organization::events()}
 * @property ?Organization $parentOrganization {@see Organization::parentOrganization()}
 */
class Organization extends Model
{
    use BuildsQueryFromRequest;
    use HasDocuments;
    use HasFactory;
    use HasLocation;
    use HasResponsibleUsers;
    use HasWebsite;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'status',
        'register_entry',
        'representatives',
        'website_url',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'status' => ActiveStatus::class,
    ];

    protected $perPage = 12;

    public function events(): BelongsToMany
    {
        return $this->belongsToMany(Event::class)
            ->withTimestamps();
    }

    public function parentOrganization(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_organization_id');
    }

    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);

        $this->location()->associate($validatedData['location_id']);

        return $this->save()
            && $this->saveResponsibleUsers($validatedData);
    }

    public function getAbilityToViewResponsibilities(): Ability
    {
        return Ability::ViewResponsibilitiesOfOrganizations;
    }

    public function getRoute(): string
    {
        return route('organizations.show', $this);
    }

    public function getStoragePath(): string
    {
        return 'organizations/' . $this->id;
    }

    public static function allowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('location_id'),
        ];
    }

    public static function sortOptions(): SortOptions
    {
        return self::sortOptionsForNameAndTimeStamps();
    }
}
