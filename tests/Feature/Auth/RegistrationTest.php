<?php

namespace Tests\Feature\Auth;

use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * @covers \App\Http\Controllers\Auth\RegisteredUserController
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function testRegistrationScreenCannotBeRenderedIfNotEnabled(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(403);
    }

    public function testRegistrationScreenCanBeRenderedIfEnabled(): void
    {
        Config::set('app.features.registration', true);

        $response = $this->get('/register');

        $response->assertStatus(200);
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
