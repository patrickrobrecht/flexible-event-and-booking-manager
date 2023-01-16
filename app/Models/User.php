<?php

namespace App\Models;

use App\Models\Traits\Filterable;
use App\Models\Traits\HasAddress;
use App\Models\Traits\Searchable;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\VerifyEmailNotification;
use App\Options\Ability;
use App\Options\ActiveStatus;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * @property-read int $id
 * @property string $first_name
 * @property string $last_name
 * @property ?string $phone
 * @property string $email
 * @property-read ?Carbon $email_verified_at
 * @property ?string $password
 * @property ActiveStatus $status
 * @property ?Carbon $last_login_at
 *
 * @property-read string greeting {@see User::greeting()}
 * @property-read string $name {@see User::name()}
 *
 * @property-read Collection|Booking[] $bookings {@see self::bookings()}
 * @property-read Collection|PersonalAccessToken[] $tokens {@see HasApiTokens::tokens()}
 * @property-read Collection|UserRole[] $userRoles {@see User::userRoles()}
 */
class User extends Authenticatable implements MustVerifyEmail
{
    use Filterable;
    use HasAddress;
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'street',
        'house_number',
        'postal_code',
        'city',
        'country',
        'email',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'status' => ActiveStatus::class,
        'last_login_at' => 'datetime',
    ];

    protected $perPage = 12;

    public function greeting(): Attribute
    {
        return new Attribute(fn () => __('Hello :name', ['name' => $this->name]));
    }

    public function name(): Attribute
    {
        return new Attribute(fn () => $this->first_name . ' ' . $this->last_name);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'booked_by_user_id');
    }

    public function userRoles(): BelongsToMany
    {
        return $this->belongsToMany(UserRole::class)
                    ->withTimestamps();
    }

    public function scopeEmail(Builder $query, ...$searchTerms): Builder
    {
        return $this->scopeSearch($query, 'email', true, ...$searchTerms);
    }

    public function scopeName(Builder $query, ...$searchTerms): Builder
    {
        return $this->scopeIncludeColumns($query, ['first_name', 'last_name'], true, ...$searchTerms);
    }

    public function fillAndSave(array $validatedData): bool
    {
        $this->fill($validatedData);

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
     * @return \Illuminate\Support\Collection<Ability>
     */
    public function getAbilities(): \Illuminate\Support\Collection
    {
        $abilities = \Illuminate\Support\Collection::empty();
        foreach ($this->userRoles as $userRole) {
            $abilities->add($userRole->abilities);
        }

        return $abilities->flatten()
            ->unique()
            ->map(static fn (string $ability) => Ability::tryFrom($ability))
            ->filter()
            ->values();
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

    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmailNotification($this));
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new ResetPasswordNotification($this, $token));
    }

    public static function allowedFilters(): array
    {
        return [
            /** @see User::scopeName() */
            AllowedFilter::scope('name'),
            /** @see User::scopeEmail() */
            AllowedFilter::scope('email'),
            AllowedFilter::exact('user_role_id', 'userRoles.id'),
            AllowedFilter::exact('status')
                ->default(ActiveStatus::Active->value),
        ];
    }

    public static function defaultSorts(): array
    {
        return [
            'last_name',
            'first_name',
        ];
    }
}
