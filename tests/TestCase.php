<?php

namespace Tests;

use App\Models\User;
use App\Models\UserRole;
use Database\Seeders\UserRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;

    protected function actingAsAdmin(): void
    {
        $this->seed(UserRoleSeeder::class);
        $adminRole = UserRole::query()->where('name', '=', 'Administrator')->first();
        $this->assertNotNull($adminRole);

        $adminUser = User::factory()
            ->hasAttached($adminRole)
            ->create();
        $this->assertNotNull($adminUser);

        $this->actingAs($adminUser);
    }
}
