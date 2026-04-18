<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\Traits\ResolvesSeederDependencies;
use Database\Seeders\Traits\SeedsDocuments;
use Database\Seeders\Traits\SeedsResponsibleUsers;
use Illuminate\Database\Seeder;
use Random\RandomException;

class EventSeeder extends Seeder
{
    use ResolvesSeederDependencies;
    use SeedsDocuments;
    use SeedsResponsibleUsers;

    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $locations = $this->resolveDependency(Location::class, LocationSeeder::class);
        $organizations = $this->resolveDependency(Organization::class, OrganizationSeeder::class);
        $users = $this->resolveDependency(User::class, UserSeeder::class);

        foreach ($organizations as $organization) {
            $events = Event::factory(random_int(5, 10))
                ->recycle($locations)
                ->state(fn () => ['location_id' => $locations->random()->id])
                ->for($organization)
                ->create();

            foreach ($events as $event) {
                $this->attachResponsibleUsers($event, $users, 50);
                $this->seedDocuments($event, $users, 50);
            }
        }
    }
}
