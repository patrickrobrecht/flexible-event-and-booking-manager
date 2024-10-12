<?php

namespace Tests\Feature\Http;

use App\Http\Controllers\DashboardController;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(DashboardController::class)]
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function testTheApplicationReturnsSuccessfulResponse(): void
    {
        $this->assertRouteAccessibleAsGuest('/');
    }
}
