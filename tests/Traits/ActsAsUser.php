<?php

namespace Tests\Traits;

use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\UserRoleSeeder;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

trait ActsAsUser
{
    use InteractsWithDatabase;
    use RefreshDatabase;
    use WithFaker;

    protected function actingAsAdmin(): void
    {
        $this->actingAsUserWithRole('Administrator');
    }

    protected function actingAsUserWithRole(string $roleName): void
    {
        $this->seed(UserRoleSeeder::class);
        $adminRole = UserRole::query()
            ->where('name', '=', $roleName)
            ->first();

        $adminUser = User::factory()
            ->hasAttached($adminRole)
            ->create();

        $this->actingAs($adminUser);
    }
}
