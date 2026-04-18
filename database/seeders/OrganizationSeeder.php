<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\Traits\ResolvesSeederDependencies;
use Database\Seeders\Traits\SeedsDocuments;
use Database\Seeders\Traits\SeedsResponsibleUsers;
use Illuminate\Database\Seeder;

class OrganizationSeeder extends Seeder
{
    use ResolvesSeederDependencies;
    use SeedsDocuments;
    use SeedsResponsibleUsers;

    public function run(): void
    {
        $locations = $this->resolveDependency(Location::class, LocationSeeder::class);
        $allUsers = $this->resolveDependency(User::class, UserSeeder::class);

        $organizations = Organization::factory(5)
            ->recycle($locations)
            ->state(fn () => ['location_id' => $locations->random()->id])
            ->create();

        foreach ($organizations as $organization) {
            $this->attachResponsibleUsers($organization, $allUsers, 70);
            $this->seedDocuments($organization, $allUsers, 70);
        }
    }
}
