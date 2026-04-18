<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\User;
use Database\Seeders\Traits\ResolvesSeederDependencies;
use Database\Seeders\Traits\SeedsDocuments;
use Illuminate\Database\Seeder;
use Random\RandomException;

class LocationSeeder extends Seeder
{
    use ResolvesSeederDependencies;
    use SeedsDocuments;

    /**
     * @throws RandomException
     */
    public function run(): void
    {
        $users = $this->resolveDependency(User::class, UserSeeder::class);

        $locations = Location::factory(10)
            ->create();

        foreach ($locations as $location) {
            $this->seedDocuments($location, $users, 60);
        }
    }
}
