<?php

namespace Database\Seeders\Traits;

use App\Enums\FileType;
use App\Models\Document;
use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Random\RandomException;

trait SeedsDocuments
{
    /**
     * @param Collection<int, User> $users
     *
     * @throws RandomException
     */
    protected function seedDocuments(Event|EventSeries|Location|Organization $model, Collection $users, int $probability): void
    {
        if (fake()->boolean($probability)) {
            $count = random_int(1, 4);

            Document::factory($count)
                ->forReference($model)
                ->state(fn () => [
                    'file_type' => FileType::Text,
                    'uploaded_by_user_id' => $users->random()->id,
                ])
                ->create();
        }
    }
}
