<?php

namespace Tests\Feature\Console\Commands;

use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('serial')]
class DatabaseCommandsTest extends TestCase
{
    public function testMigrationsAndRollback(): void
    {
        $this->artisan('migrate')
            /** @phpstan-ignore method.nonObject */
            ->expectsOutputToContain('Nothing to migrate.')
            ->assertSuccessful();

        $this->artisan('migrate:rollback')
            /** @phpstan-ignore method.nonObject */
            ->expectsOutputToContain('Rolling back migrations.')
            ->doesntExpectOutputToContain('FAIL')
            ->assertSuccessful();
    }

    public function testSeeders(): void
    {
        $this->artisan('db:seed')
            /** @phpstan-ignore method.nonObject */
            ->expectsOutputToContain('Seeding database.')
            ->doesntExpectOutputToContain('FAIL')
            ->assertSuccessful();
    }
}
