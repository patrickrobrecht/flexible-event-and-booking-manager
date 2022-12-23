<?php

namespace Database\Seeders;

use App\Models\UserRole;
use App\Options\Ability;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run(): void
    {
        $this->createUserRole(__('Administrator'), Ability::cases());
        $this->createUserRole(__('User'), [
            Ability::EditAccount,
        ]);
    }

    private function createUserRole(string $name, array $abilities): void
    {
        $userRole = new UserRole();
        $userRole->name = $name;
        $userRole->abilities = $abilities;
        $userRole->save();
    }
}
