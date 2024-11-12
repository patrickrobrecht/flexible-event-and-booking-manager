<?php

namespace Tests\Feature\Http\Auth;

use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(ConfirmablePasswordController::class)]
class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function testUserCanViewConfirmPasswordForm(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    public function testUserCanConfirmPassword(): void
    {
        $user = User::factory()->create();

        /** @noinspection PhpUnhandledExceptionInspection */
        $this->actingAs($user)
            ->post('/confirm-password', [
                'password' => 'password',
            ])
            ->assertRedirect()
            ->assertSessionHasNoErrors();
    }

    public function testUserCannotConfirmWithInvalidPassword(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/confirm-password', [
                'password' => 'wrong-password',
            ])
            ->assertSessionHasErrors();
    }
}
