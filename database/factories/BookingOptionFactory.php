<?php

namespace Database\Factories;

use App\Models\BookingOption;
use App\Options\BookingRestriction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BookingOption>
 */
class BookingOptionFactory extends Factory
{
    public function definition(): array
    {
        $name = __('Booking option') . ' #' . $this->faker->unique()->randomNumber();

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'description' => $this->faker->sentences(2, true),
            'available_from' => $this->faker->dateTimeBetween('-30 years', '-1 day'),
            'available_until' => $this->faker->dateTimeBetween('+1 day', '+30 days'),
            'price' => $this->faker->randomFloat(2, 5, 100),
        ];
    }

    public function availabilityStartingInFuture(): static
    {
        return $this->state(function (array $attributes) {
            $startDate = Carbon::create($this->faker->dateTimeBetween('+1 days', '+5 days'));
            return [
                'available_from' => $startDate,
                'available_until' => $startDate->addDays($this->faker->numberBetween(3, 31)),
            ];
        });
    }

    public function availabilityEndedInPast(): static
    {
        return $this->state(function (array $attributes) {
            $endDate = Carbon::create($this->faker->dateTimeBetween('-5 days', '-1 days'));
            return [
                'available_from' => $endDate->subDays($this->faker->numberBetween(3, 31)),
                'available_until' => $endDate,
            ];
        });
    }

    public function maximumBookings(int $maximumBookings): static
    {
        return $this->state(fn (array $attributes) => [
            'maximum_bookings' => $maximumBookings,
        ]);
    }

    public function restriction(array|BookingRestriction $bookingRestriction): static
    {
        return $this->state(fn (array $attributes) => [
            'restrictions' => [$bookingRestriction],
        ]);
    }

    public function withoutPrice(): static
    {
        return $this->state(fn (array $attributes) => [
            'price' => null,
        ]);
    }
}
