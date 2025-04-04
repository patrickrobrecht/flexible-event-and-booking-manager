<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Http\Controllers\ApiDocumentationController;
use App\Listeners\CacheOpenApiDocListener;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(ApiDocumentationController::class)]
#[CoversClass(CacheOpenApiDocListener::class)]
class ApiDocumentationControllerTest extends TestCase
{
    public function testUserCanViewApiDocumentationOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('api-docs', Ability::ManagePersonalAccessTokens);
    }

    public function testUserCanViewYamlFileOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('api-docs/spec', Ability::ManagePersonalAccessTokens);
    }

    public function testYamlFileContainsReplacements(): void
    {
        $this->actingAsUserWithAbility(Ability::ManagePersonalAccessTokens);
        $this->get('api-docs/spec')
            ->assertOk()
            ->assertSeeText('Test App')
            ->assertSeeText('staging environment');
    }

    #[DataProvider('booleanProvider')]
    public function testYamlFileCache(bool $isDebugEnabled): void
    {
        Config::set('app.debug', $isDebugEnabled);
        Cache::forget('open-api-spec');
        $this->assertUserCanGetWithAbility('api-docs/spec', Ability::ManagePersonalAccessTokens);
        // .yaml file contents is cached if and only if debugging is not enabled.
        $this->assertEquals(!$isDebugEnabled, Cache::has('open-api-spec'));
    }

    public static function booleanProvider(): array
    {
        return [
            [false],
            [true],
        ];
    }
}
