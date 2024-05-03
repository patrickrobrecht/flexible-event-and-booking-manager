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
            ->withTimestamps()
            ->orderBy('last_name')
            ->orderBy('first_name');
    }

    public function saveResponsibleUsers(array $userIds): array
    {
        return $this->responsibleUsers()->sync($userIds);
    }
}
