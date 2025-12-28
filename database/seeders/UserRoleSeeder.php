<?php

namespace Database\Seeders;

use App\Enums\Ability;
use App\Models\UserRole;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->createUserRole('Administrator', Ability::cases());
        $this->createUserRole('User', [
            Ability::EditAccount,
        ]);
    }

    /**
     * @param Ability[] $abilities
     */
    private function createUserRole(string $name, array $abilities): void
    {
        $userRole = new UserRole();
        $userRole->name = $name;
        $userRole->abilities = Ability::values($abilities);
        $userRole->save();
    }
}
