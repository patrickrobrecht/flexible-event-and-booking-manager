<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Options\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        $managerNames = array_map(
            static fn () => fake()->name(),
            range(1, fake()->numberBetween(0, 3))
        );

        return [
            'name' => fake()->company(),
            'representatives' => implode(', ', $managerNames),
            'status' => ActiveStatus::Active,
            'website_url' => fake()->optional()->url(),
        ];
    }
}
