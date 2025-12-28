<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\EventSeries;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
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

        /** @var Collection<int, Location> $locations */
        $locations = Location::factory(5)->create();
        /** @var Location $randomLocation */
        $randomLocation = fake()->randomElement($locations);

        /** @var Collection<int, Organization> $organizations */
        $organizations = Organization::factory(5)
            ->for($randomLocation)
            ->create();
        /** @var Organization $randomOrganization */
        $randomOrganization = fake()->randomElement($organizations);

        Event::factory(5)
            ->for($randomLocation)
            ->for($randomOrganization)
            ->create();

        EventSeries::factory(3)
            ->for($randomOrganization)
            ->create();
    }
}
