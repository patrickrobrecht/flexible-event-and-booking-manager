<?php

/** @noinspection AnonymousFunctionStaticInspection */

namespace Database\Factories;

use App\Enums\ActiveStatus;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    public const string DEFAULT_PASSWORD = 'Password-123456!';

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

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
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make(self::DEFAULT_PASSWORD),
            'remember_token' => Str::random(10),
            'status' => ActiveStatus::Active,
        ];
    }

    public function status(ActiveStatus $status): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => $status,
        ]);
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
