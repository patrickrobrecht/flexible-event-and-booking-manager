<?php

namespace Database\Factories;

use App\Enums\Ability;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserRole>
 */
class UserRoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'abilities' => fake()->randomElements(Ability::cases(), $this->faker->numberBetween(1, count(Ability::cases()))),
        ];
    }
}
