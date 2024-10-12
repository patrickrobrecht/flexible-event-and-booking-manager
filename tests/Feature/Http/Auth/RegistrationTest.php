<?php

namespace Tests\Feature\Http\Auth;

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(RegisteredUserController::class)]
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistrationScreenCannotBeRenderedIfNotEnabled(): void
    {
        Config::set('app.features.registration', false);

        $this->get('/register')->assertForbidden();
    }

    public function testRegistrationScreenCanBeRenderedIfEnabled(): void
    {
        Config::set('app.features.registration', true);

        $this->assertRouteAccessibleAsGuest('/register');
    }

    public function testNewUsersCanRegister(): void
    {
        Config::set('app.features.registration', true);
        Config::set('app.urls.terms_and_conditions', '');

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function testNewUsersCannotRegisterWithoutAcceptingTheTerms(): void
    {
        Config::set('app.features.registration', true);

        $response = $this->post('/register', [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(302);
        $response->assertSessionHasErrors();
    }
}
