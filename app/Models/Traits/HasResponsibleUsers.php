<?php

namespace App\Models\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

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
                'position',
                'sort',
            ])
            ->withTimestamps()
            ->orderBy('sort')
            ->orderBy('last_name')
            ->orderBy('first_name');
    }

    public function saveResponsibleUsers(array $validatedData): bool
    {
        $this->responsibleUsers()->sync($validatedData['responsible_user_id'] ?? []);

        foreach ($validatedData['responsible_user_data'] ?? [] as $userId => $pivotData) {
            if ($this->responsibleUsers()->updateExistingPivot($userId, $pivotData) !== 1) {
                return false;
            }
        }

        return true;
    }
}
