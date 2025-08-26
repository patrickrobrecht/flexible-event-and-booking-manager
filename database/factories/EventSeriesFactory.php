<?php

namespace Database\Factories;

use App\Enums\Visibility;
use App\Models\EventSeries;
use Database\Factories\Traits\BelongsToOrganization;
use Database\Factories\Traits\HasVisibility;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<EventSeries>
 */
class EventSeriesFactory extends Factory
{
    use BelongsToOrganization;
    use HasVisibility;

    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'visibility' => fake()->randomElement(Visibility::values()),
        ];
    }
}
