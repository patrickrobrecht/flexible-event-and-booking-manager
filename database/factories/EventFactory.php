<?php

namespace Database\Factories;

use App\Enums\Visibility;
use App\Models\Event;
use Carbon\Carbon;
use Database\Factories\Traits\BelongsToLocation;
use Database\Factories\Traits\BelongsToOrganization;
use Database\Factories\Traits\HasVisibility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    use BelongsToLocation;
    use BelongsToOrganization;
    use HasVisibility;

    public function definition(): array
    {
        /** @var string $name */
        $name = fake()->unique()->words(3, true);
        $startedAt = (new Carbon(fake()->dateTimeBetween('now', '+5 years')))
            ->setMinutes(fake()->numberBetween(0, 3) * 15);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => fake()->text(),
            'visibility' => fake()->randomElement(Visibility::values()),
            'started_at' => $startedAt,
            'finished_at' => $startedAt->clone()->addHours(fake()->numberBetween(3, 168)),
            'website_url' => fake()->optional()->url(),
        ];
    }
}
