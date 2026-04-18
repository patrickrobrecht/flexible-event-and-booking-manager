<?php

namespace Database\Seeders\Traits;

use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Random\RandomException;

trait SeedsResponsibleUsers
{
    /**
     * @param Collection<int, User> $users
     *
     * @throws RandomException
     */
    protected function attachResponsibleUsers(Event|EventSeries|Organization $model, Collection $users, int $probability): void
    {
        if (fake()->boolean($probability)) {
            $model->responsibleUsers()->attach(
                $users->random(min($users->count(), random_int(1, 3)))
                    ->pluck('id')
                    ->toArray(),
                [
                    'publicly_visible' => fake()->boolean(),
                    'position' => fake()->jobTitle(),
                    'sort' => 0,
                ]
            );
        }
    }
}
