<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Http\Controllers\ApiDocumentationController;
use App\Listeners\CacheOpenApiDocListener;
use PHPUnit\Framework\Attributes\CoversClass;
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
            ->assertSeeText('Test App');
    }
}
