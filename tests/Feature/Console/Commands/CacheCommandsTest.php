<?php

namespace Tests\Feature\Console\Commands;

use App\Listeners\CacheOpenApiDocListener;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\CoversClass;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Tests\TestCase;

#[CoversClass(CacheOpenApiDocListener::class)]
class CacheCommandsTest extends TestCase
{
    public function testConfigCache(): void
    {
        Artisan::call('config:clear');

        $result = Artisan::call('config:cache');
        self::assertEquals(Command::SUCCESS, $result);
        self::assertFileExists(base_path('bootstrap/cache/config.php'));

        Artisan::call('config:clear');
    }

    public function testYamlFileIsUpdatedWhenConfigIsCached(): void
    {
        Cache::forget('open-api-spec');

        event(new CommandFinished('config:cache', new ArrayInput([]), new NullOutput(), 0));

        self::assertNotNull(Cache::get('open-api-spec'));
    }

    public function testEventCache(): void
    {
        Artisan::call('event:clear');

        $result = Artisan::call('event:cache');
        self::assertEquals(Command::SUCCESS, $result);
        self::assertFileExists(base_path('bootstrap/cache/events.php'));

        Artisan::call('event:clear');
    }

    public function testRouteCache(): void
    {
        Artisan::call('route:clear');

        $result = Artisan::call('route:cache');
        self::assertEquals(Command::SUCCESS, $result);
        self::assertFileExists(base_path('bootstrap/cache/routes-v7.php'));

        Artisan::call('route:clear');
    }

    public function testViewCache(): void
    {
        Artisan::call('view:clear');

        $result = Artisan::call('view:cache');
        self::assertEquals(Command::SUCCESS, $result);
        $cachedViewsPath = storage_path('framework/views');
        self::assertDirectoryExists($cachedViewsPath);
        self::assertGreaterThan(2, count(scandir($cachedViewsPath)), 'The view cache directory is empty.');

        Artisan::call('view:clear');
    }
}
