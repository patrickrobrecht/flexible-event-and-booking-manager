<?php

namespace App\Models\Traits;

use App\Models\User;
use App\Options\Ability;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Facades\Auth;

/**
 * @property-read $responsibleUsers {@see self::responsibleUsers()}
 *
 * @mixin Model
 */
trait HasResponsibleUsers
{
    public function responsibleUsers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'responsible_for', 'user_responsibilities')
            ->withPivot([
                'publicly_visible',
                'position',
                'sort',
            ])
            ->withTimestamps()
            ->orderBy('sort')
            ->orderBy('last_name')
            ->orderBy('first_name');
    }

    abstract public function getAbilityToViewResponsibilities(): Ability;

    public function getResponsibleUsersVisibleForCurrentUser(): Collection
    {
        $currentUser = Auth::user();
        if (
            isset($currentUser) && (
                $currentUser->hasAbility($this->getAbilityToViewResponsibilities())
                || $currentUser->isResponsibleFor($this)
            )
        ) {
            return $this->responsibleUsers;
        }

        return $this->getPubliclyVisibleResponsibleUsers();
    }

    /**
     * @return Collection<User>
     */
    public function getPubliclyVisibleResponsibleUsers(): Collection
    {
        return $this->responsibleUsers->filter(fn (User $responsibleUser) => self::isPubliclyVisible($responsibleUser));
    }

    public function hasPubliclyVisibleResponsibleUsers(): bool
    {
        return $this->responsibleUsers->first(fn (User $responsibleUser) => self::isPubliclyVisible($responsibleUser)) !== null;
    }

    public function saveResponsibleUsers(array $validatedData): array
    {
        $responsibleUsers = [];
        foreach ($validatedData['responsible_user_id'] ?? [] as $responsibleUserId) {
            $pivotData = $validatedData['responsible_user_data'][$responsibleUserId] ?? [];
            $pivotData['publicly_visible'] ??= false;
            $responsibleUsers[$responsibleUserId] = $pivotData;
        }

        return $this->responsibleUsers()->sync($responsibleUsers);
    }

    private static function isPubliclyVisible(User $responsibleUser): bool
    {
        return isset($responsibleUser->pivot->publicly_visible)
            && $responsibleUser->pivot->publicly_visible;
    }
}
