<?php

namespace Database\Factories;

use App\Models\Material;
use App\Models\Organization;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Material>
 */
class MaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'description' => $this->faker->optional()->sentences(5, true),
        ];
    }

    public function forOrganization(Organization $organization): static
    {
        return $this->for($organization);
    }
}
