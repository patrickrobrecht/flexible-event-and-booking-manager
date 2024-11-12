<?php

namespace Database\Factories;

use App\Models\Booking;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Booking>
 */
class BookingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $firstName = fake()->firstName();
        $lastName = fake()->lastName();

        return [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'street' => fake()->streetName(),
            'house_number' => fake()->buildingNumber(),
            'postal_code' => fake()->postcode(),
            'city' => fake()->city(),
            'country' => fake()->country(),
            'phone' => fake()->phoneNumber(),
            'email' => sprintf('%s.%s@%s', Str::slug($firstName), Str::slug($lastName), fake()->unique()->domainName()),
            'date_of_birth' => fake()->date(),
        ];
    }

    public function withoutDateOfBirth(): static
    {
        return $this->state(fn (array $attributes) => [
            'date_of_birth' => null,
        ]);
    }
}
