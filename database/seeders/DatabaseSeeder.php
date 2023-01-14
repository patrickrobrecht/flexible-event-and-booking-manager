<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run(): void
    {
        $this->call(UserRoleSeeder::class);

        $userRoles = UserRole::all();
        foreach ($userRoles as $userRole) {
            User::factory(5)
                ->hasAttached($userRole)
                ->create();
        }

        $locations = Location::factory(5)->create();

        $organizations =  Organization::factory(5)
            ->for(fake()->randomElement($locations))
            ->create();

        Event::factory(5)
            ->for(fake()->randomElement($locations))
            ->hasAttached(fake()->randomElements($organizations, fake()->numberBetween(1, 3)))
            ->create();

        EventSeries::factory(3)
            ->create();
    }
}
