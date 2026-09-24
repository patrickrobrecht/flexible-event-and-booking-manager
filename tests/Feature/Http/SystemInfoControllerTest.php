<?php

namespace Tests\Feature\Http;

use App\Enums\Ability;
use Tests\TestCase;

class SystemInfoControllerTest extends TestCase
{
    public function testUserCanViewSystemInformationOnlyWithCorrectAbility(): void
    {
        $this->assertUserCanGetOnlyWithAbility('system-info', Ability::ViewSystemInformation);
    }
}
