<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use App\Http\Controllers\SystemInfoController;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(SystemInfoController::class)]
class SystemInfoControllerTest extends TestCase
{
    public function testUserCanViewApiDocumentationOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('system-info', Ability::ViewSystemInformation);
    }
}
