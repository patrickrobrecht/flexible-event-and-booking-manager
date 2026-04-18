<?php

namespace Database\Seeders;

use App\Enums\BookingStatus;
use App\Models\Booking;
use App\Models\BookingOption;
use App\Models\User;
use Database\Seeders\Traits\ResolvesSeederDependencies;
use Illuminate\Database\Seeder;
use Random\RandomException;

class BookingSeeder extends Seeder
{
    use ResolvesSeederDependencies;

    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $users = $this->resolveDependency(User::class, UserSeeder::class);
        $bookingOptions = $this->resolveDependency(BookingOption::class, BookingOptionSeeder::class);

        foreach ($bookingOptions as $bookingOption) {
            Booking::factory(random_int(1, $bookingOption->maximum_bookings ?? 99))
                ->for($bookingOption)
                ->state(function () use ($users) {
                    $status = fake()->boolean(80) ? BookingStatus::Confirmed : BookingStatus::Waiting;

                    // Assign a user to the booking with 40% probability.
                    $bookedByUserId = fake()->boolean(40) ? $users->random()->id : null;

                    return [
                        'status' => $status,
                        'booked_by_user_id' => $bookedByUserId,
                        'booked_at' => fake()->dateTimeBetween('-1 month'),
                    ];
                })
                ->create();
        }
    }
}
