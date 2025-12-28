<?php

namespace Database\Factories;

use App\Enums\MaterialStatus;
use App\Models\Material;
use App\Models\Organization;
use App\Models\StorageLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Material>
 */
class MaterialFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->word(),
            'description' => $this->faker->optional()->sentences(5, true),
        ];
    }

    public function forOrganization(?Organization $organization = null): static
    {
        return $this->for($organization ?? Organization::factory()->forLocation()->create());
    }

    public function hasStorageLocations(?int $count = null): static
    {
        return $this->hasAttached(
            StorageLocation::factory()
                ->count($count ?? $this->faker->numberBetween(0, 5)),
            [
                'material_status' => $this->faker->randomElement(MaterialStatus::cases()),
                'stock' => $this->faker->optional()->numberBetween(1, 100),
                'remarks' => $this->faker->optional()->text(),
            ]
        );
    }
}
