<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\Traits\ResolvesSeederDependencies;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    use ResolvesSeederDependencies;

    public function run(): void
    {
        $userRoles = $this->resolveDependency(UserRole::class, UserRoleSeeder::class);

        foreach ($userRoles as $userRole) {
            User::factory(5)
                ->hasAttached($userRole)
                ->create();
        }
    }
}
