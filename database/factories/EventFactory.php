<?php

namespace Database\Factories;

use App\Models\Event;
use App\Options\Visibility;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);
        $startedAt = (new Carbon(fake()->dateTimeBetween('now', '+5 years')))
            ->setMinutes(fake()->randomNumber(1, 3) * 15);

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

    public function visibility(?Visibility $visibility): static
    {
        if ($visibility === null) {
            return $this;
        }

        return $this->state(fn (array $attributes) => [
            'visibility' => $visibility,
        ]);
    }
}
