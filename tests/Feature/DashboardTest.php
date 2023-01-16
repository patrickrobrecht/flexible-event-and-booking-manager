<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\DashboardController
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function testTheApplicationReturnsSuccessfulResponse(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
