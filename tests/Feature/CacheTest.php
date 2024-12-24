<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Artisan;
use Symfony\Component\Console\Command\Command;
use Tests\TestCase;

class CacheTest extends TestCase
{
    public function testConfigCache(): void
    {
        Artisan::call('config:clear');

        $result = Artisan::call('config:cache');
        $this->assertEquals(Command::SUCCESS, $result);
        $this->assertFileExists(base_path('bootstrap/cache/config.php'));

        Artisan::call('config:clear');
    }

    public function testEventCache(): void
    {
        Artisan::call('event:clear');

        $result = Artisan::call('event:cache');
        $this->assertEquals(Command::SUCCESS, $result);
        $this->assertFileExists(base_path('bootstrap/cache/events.php'));

        Artisan::call('event:clear');
    }

    public function testRouteCache(): void
    {
        Artisan::call('route:clear');

        $result = Artisan::call('route:cache');
        $this->assertEquals(Command::SUCCESS, $result);
        $this->assertFileExists(base_path('bootstrap/cache/routes-v7.php'));

        Artisan::call('route:clear');
    }

    public function testViewCache(): void
    {
        Artisan::call('view:clear');

        $result = Artisan::call('view:cache');
        $this->assertEquals(Command::SUCCESS, $result);
        $cachedViewsPath = storage_path('framework/views');
        $this->assertDirectoryExists($cachedViewsPath);
        $this->assertGreaterThan(2, count(scandir($cachedViewsPath)), 'The view cache directory is empty.');

        Artisan::call('view:clear');
    }
}
