<?php

namespace Database\Factories;

use App\Models\Organization;
use App\Options\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'status' => ActiveStatus::Active,
            'website_url' => $this->faker->optional()->url(),
        ];
    }
}
