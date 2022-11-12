<?php

namespace Database\Seeders;

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
    }
}
