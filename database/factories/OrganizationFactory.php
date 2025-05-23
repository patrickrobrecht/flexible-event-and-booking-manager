<?php

namespace Database\Factories;

use App\Enums\ActiveStatus;
use App\Models\Location;
use App\Models\Organization;
use Database\Factories\Traits\BelongsToLocation;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Organization>
 */
class OrganizationFactory extends Factory
{
    use BelongsToLocation;

    public function definition(): array
    {
        $name = $this->faker->company();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'status' => ActiveStatus::Active,
            'phone' => $this->faker->optional()->phoneNumber(),
            'email' => sprintf('%s@%s', Str::slug($name), $this->faker->unique()->domainName()),
            'website_url' => $this->faker->optional()->url(),
        ];
    }

    public function forLocation(?Location $location = null): static
    {
        return $this->for($location ?? Location::factory()->create());
    }

    public function withBankAccount(): static
    {
        return $this->state(fn (array $attributes) => [
            'bank_account_holder' => $this->faker->company(),
            'iban' => $this->faker->iban(),
            'bank_name' => $this->faker->company(),
        ]);
    }
}
