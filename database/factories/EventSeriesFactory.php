<?php

namespace Database\Factories;

use App\Models\EventSeries;
use App\Options\Visibility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EventSeries>
 */
class EventSeriesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'visibility' => fake()->randomElement(Visibility::keys()),
        ];
    }
}
