<?php

namespace Database\Seeders;

use App\Models\EventSeries;
use App\Models\Organization;
use App\Models\User;
use Database\Seeders\Traits\ResolvesSeederDependencies;
use Database\Seeders\Traits\SeedsDocuments;
use Database\Seeders\Traits\SeedsResponsibleUsers;
use Illuminate\Database\Seeder;
use Random\RandomException;

class EventSeriesSeeder extends Seeder
{
    use ResolvesSeederDependencies;
    use SeedsDocuments;
    use SeedsResponsibleUsers;

    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $organizations = $this->resolveDependency(Organization::class, OrganizationSeeder::class);
        $users = $this->resolveDependency(User::class, UserSeeder::class);

        foreach ($organizations as $organization) {
            $eventSeries = EventSeries::factory(random_int(3, 5))
                ->for($organization)
                ->create();

            foreach ($eventSeries as $series) {
                $this->attachResponsibleUsers($series, $users, 40);
                $this->seedDocuments($series, $users, 40);
            }
        }
    }
}
