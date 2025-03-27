<?php

namespace Tests\Feature\Http\Api;

use App\Enums\Ability;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;

class AccountApiTest extends TestCase
{
    use ActsWithToken;
    use RefreshDatabase;

    public function testAccountDataCanBeRequestedWithCorrectAbility(): void
    {
        $this->assertTokenCanGetWithAbility('api/account', Ability::ViewAccount);
    }

    public function testAccountDataCannotBeRequestedWithoutCorrectAbility(): void
    {
        $this->assertTokenCannotGetWithoutAbility('api/account', Ability::ViewAccount);
    }
}
