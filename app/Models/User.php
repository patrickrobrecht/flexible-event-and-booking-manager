<?php

namespace App\Models;

use App\Enums\Ability;
use App\Enums\ActiveStatus;
use App\Enums\FilterValue;
use App\Models\QueryBuilder\BuildsQueryFromRequest;
use App\Models\QueryBuilder\SortOptions;
use App\Models\Traits\FiltersByRelationExistence;
use App\Models\Traits\HasAddress;
use App\Models\Traits\HasFullName;
use App\Models\Traits\HasPhone;
use App\Models\Traits\Searchable;
use App\Notifications\AccountCreatedNotification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\Enums\SortDirection;

/**
 * @property-read int $id
 * @property string $first_name
 * @property string $last_name
 * @property ?Carbon $date_of_birth
 * @property ?string $phone
 * @property string $email
 * @property ?Carbon $email_verified_at
 * @property ?string $password
 * @property ActiveStatus $status
 * @property ?Carbon $last_login_at
 * @property-read Booking[]|Collection $bookings {@see self::bookings()}
 * @property-read Booking[]|Collection $bookingsTrashed {@see self::bookingsTrashed()}
 * @property-read Collection|Document[] $documents {@see self::documents()}
 * @property-read Collection|Event[] $responsibleForEvents {@see self::responsibleForEvents()}
 * @property-read Collection|EventSeries[] $responsibleForEventSeries {@see self::responsibleForEventSeries()}
 * @property-read Collection|Organization[] $responsibleForOrganizations {@see self::responsibleForOrganizations()}
 * @property-read Collection|PersonalAccessToken[] $tokens {@see HasApiTokens::tokens()}
 * @property-read Collection|UserRole[] $userRoles {@see self::userRoles()}
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use BuildsQueryFromRequest;
    use FiltersByRelationExistence;
    use HasAddress;
    use HasApiTokens;
    use HasFactory;
    use HasFullName;
    use HasPhone;
    use Notifiable;
    use Searchable;

    protected $casts = [
        'date_of_birth' => 'date',
        'email_verified_at' => 'datetime',
        'status' => ActiveStatus::class,
        'last_login_at' => 'datetime',
        // counts
        'bookings_count' => 'integer',
        'bookings_trashed_count' => 'integer',
        'documents_count' => 'integer',
        'responsible_for_events_count' => 'integer',
        'responsible_for_event_series_count' => 'integer',
        'responsible_for_organizations_count' => 'integer',
        'tokens_count' => 'integer',
    ];

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'street',
        'house_number',
        'postal_code',
        'city',
        'country',
        'date_of_birth',
        'email',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'booked_by_user_id')
            ->orderByDesc('booked_at')
            ->orderByDesc('created_at');
    }

    public function bookingsTrashed(): HasMany
    {
        /** @phpstan-ignore-next-line method.notFound */
        return $this->bookings()
            ->onlyTrashed();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class, 'uploaded_by_user_id')
            ->orderBy('title');
    }

    /**
     * @param class-string<Model> $class
     */
    private function responsibleFor(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'responsible_for', 'user_responsibilities')
            ->withPivot([
                'publicly_visible',
                'position',
                'sort',
            ]);
    }

    public function responsibleForEvents(): MorphToMany
    {
        return $this->responsibleFor(Event::class)
            ->orderByDesc('started_at')
            ->orderByDesc('finished_at')
            ->orderBy('name');
    }

    public function responsibleForEventSeries(): MorphToMany
    {
        return $this->responsibleFor(EventSeries::class)
            ->orderBy('name');
    }

    public function responsibleForOrganizations(): MorphToMany
    {
        return $this->responsibleFor(Organization::class)
            ->orderBy('name');
    }

    public function userRoles(): BelongsToMany
    {
        return $this->belongsToMany(UserRole::class)
            ->withTimestamps();
    }

    public function scopeEmail(Builder $query, string ...$searchTerms): Builder
    {
        return $this->scopeSearch($query, 'email', true, ...$searchTerms);
    }

    public function scopeName(Builder $query, string ...$searchTerms): Builder
    {
        return $this->scopeIncludeColumns($query, ['first_name', 'last_name'], true, ...$searchTerms);
    }

    public function scopeUserRole(Builder $query, int|string $userRoleId): Builder
    {
        return $this->scopeRelation($query, $userRoleId, 'userRoles', fn (Builder $q) => $q->where('user_role_id', '=', $userRoleId));
    }

    public function deleteWithRelations(): bool
    {
        $this->userRoles()->detach();
        $this->tokens()->delete();
        return $this->delete() === true;
    }

    /**
     * @param array<string, mixed> $validatedData
     */
    public function fillAndSave(array $validatedData): bool
    {
        /** @phpstan-var array{password: ?string, user_role_id: int[]|null} $validatedData */
        $this->fill($validatedData);

        if ($this->isDirty('email')) {
            // If the email address is changed, reset verification.
            $this->email_verified_at = null;
        }

        if (isset($validatedData['password'])) {
            $this->password = Hash::make($validatedData['password']);
        }

        if (!$this->save()) {
            return false;
        }

        if (isset($validatedData['user_role_id'])) {
            $changes = $this->userRoles()->sync($validatedData['user_role_id']);
            if (count($changes['attached']) > 0 || count($changes['updated']) > 0 || count($changes['detached']) > 0) {
                $this->touch();
            }
        }

        return true;
    }

    /**
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function getAbilitiesAsStrings(): \Illuminate\Support\Collection
    {
        $abilities = \Illuminate\Support\Collection::empty();
        foreach ($this->userRoles as $userRole) {
            $abilities->add($userRole->abilities);
        }

        /** @phpstan-ignore-next-line return.type */
        return $abilities->flatten()
            ->unique();
    }

    public function hasAbility(Ability $ability): bool
    {
        if ($this->status === ActiveStatus::Active) {
            foreach ($this->userRoles as $userRole) {
                if ($userRole->hasAbility($ability)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function isResponsibleFor(Event|EventSeries|Organization $model): bool
    {
        return ($model->responsibleUsers ?? Collection::empty())
            ->pluck('id')
            ->contains($this->id);
    }

    public function loadProfileData(): self
    {
        $this->load([
            'bookings.bookingOption.event.location',
            'documents.reference',
            'responsibleForEvents' => fn (MorphToMany $events) => $events
                /** @phpstan-ignore argument.type */
                ->with([
                    'bookingOptions' => fn (HasMany $bookingOptions) => $bookingOptions
                        ->withCount([
                            'bookings',
                        ]),
                ])
                ->withCount([
                    'documents',
                    'groups',
                ]),
            'responsibleForEvents.eventSeries',
            'responsibleForEvents.location',
            'responsibleForEvents.parentEvent',
            'responsibleForEventSeries' => fn (MorphToMany $eventSeries) => $eventSeries
                ->withCount([
                    'documents',
                    'events',
                ])
                ->withMin('events', 'started_at')
                ->withMax('events', 'started_at')
                ->withCasts([
                    'events_min_started_at' => 'datetime',
                    'events_max_started_at' => 'datetime',
                ]),
            'responsibleForOrganizations.location',
        ]);

        // Set backwards relation for documents.
        $this->documents->each(fn (Document $document) => $document->setRelation('uploadedByUser', $this));

        return $this;
    }

    public function sendAccountCreatedNotification(): void
    {
        $this->notify(new AccountCreatedNotification($this));
        Log::info("Account created email sent to {$this->email}");
    }

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification($this));
        Log::info("Verification email sent to {$this->email}");
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($this, $token));
        Log::info("Reset password email sent to {$this->email}");
    }

    /**
     * @return AllowedFilter[]
     */
    public static function allowedFilters(): array
    {
        return [
            /** @see User::scopeName() */
            AllowedFilter::scope('name'),
            /** @see User::scopeEmail() */
            AllowedFilter::scope('email'),
            /** @see User::scopeUserRole() */
            AllowedFilter::scope('user_role_id', 'userRole')
                ->default(FilterValue::All->value),
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
            FilterValue::With->value => __('with at least one user'),
            FilterValue::Without->value => __('without users'),
        ];
    }

    public static function sortOptions(): SortOptions
    {
        return (new SortOptions())
            ->addBothDirections(
                __('Name'),
                AllowedSort::callback(
                    'name',
                    static fn (Builder $query, bool $descending, string $property) => $query
                        ->orderBy('last_name', $descending ? SortDirection::DESCENDING : SortDirection::ASCENDING)
                        ->orderBy('first_name', $descending ? SortDirection::DESCENDING : SortDirection::ASCENDING)
                ),
                true
            )
            ->merge(self::sortOptionsForTimeStamps())
            ->addBothDirections(__('Time of last login'), AllowedSort::field('last_login_at'))
            ->addBothDirections(__('Number of event bookings'), self::allowedSortForRelationCount('bookings'));
    }
}
