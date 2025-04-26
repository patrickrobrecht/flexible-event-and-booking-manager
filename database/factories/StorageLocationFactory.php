<?php

namespace Database\Factories;

use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StorageLocation>
 */
class StorageLocationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->optional()->sentences(5, true),
            'packaging_instructions' => $this->faker->optional(0.2)->sentences(3, true),
        ];
    }

    public function forParentStorageLocation(?StorageLocation $parentStorageLocation): static
    {
        if ($parentStorageLocation === null) {
            return $this;
        }

        return $this->for($parentStorageLocation, 'parentStorageLocation');
    }
}
