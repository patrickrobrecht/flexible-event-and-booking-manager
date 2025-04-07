<?php

namespace Tests\Feature\Http\Api;

use App\Enums\Ability;
use App\Exceptions\Handler;
use App\Http\Controllers\AccountController;
use App\Http\Middleware\AcceptLanguageMiddleware;
use App\Http\Middleware\ThrottleRequestsMiddleware;
use App\Models\PersonalAccessToken;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Tests\Feature\Http\Middleware\JsonApiMiddlewareTest;
use Tests\TestCase;
use Tests\Traits\ActsWithToken;

#[CoversClass(AcceptLanguageMiddleware::class)]
#[CoversClass(AccountController::class)]
#[CoversClass(Handler::class)]
#[CoversClass(JsonApiMiddlewareTest::class)]
#[CoversClass(PersonalAccessToken::class)]
#[CoversClass(ThrottleRequestsMiddleware::class)]
#[CoversClass(User::class)]
class ApiTest extends TestCase
{
    use ActsWithToken;
    use RefreshDatabase;

    public function testDataCannotBeRequestedWithInvalidToken(): void
    {
        $token = $this->createTokenWithAbility(Ability::ViewOrganizations);

        $this->withHeadersForApiRequest($token->accessToken->id.'|invalidTokenString')
            ->get('api/organizations')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDataCannotBeRequestedWithExpiredToken(): void
    {
        $user = User::factory()->create();
        $token = PersonalAccessToken::createTokenFromValidated($user, [
            'name' => 'Test Token',
            'abilities' => [Ability::ViewAccount],
            'expires_at' => Carbon::yesterday()->format('Y-m-d\TH:i'),
        ]);

        $this->withHeadersForApiRequest($token->plainTextToken)
            ->get('api/organizations')
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function testDataCanBeRequestedAsJsonOrXml(): void
    {
        $this->withHeadersForApiRequestWithAbility(Ability::ViewOrganizations)
            ->withHeader('Accept', 'application/xml')
            ->get('api/organizations')
            ->assertUnsupportedMediaType();
    }

    #[DataProvider('acceptHeaders')]
    public function testDataCannotBeRequestedAsXml(string $acceptHeader): void
    {
        $this->withHeadersForApiRequestWithAbility(Ability::ViewOrganizations)
            ->withHeader('Accept', $acceptHeader)
            ->get('api/organizations')
            ->assertOk();
    }

    /**
     * @return array<int, array{string}>
     */
    public static function acceptHeaders(): array
    {
        return [
            ['*/*'],
            ['*/json'],
            ['application/json'],
            ['application/json, application/xml;q=0.9'],
            ['application/xml, */*;q=0.8'],
            ['text/json'],
        ];
    }

    #[DataProvider('acceptLanguageHeaders')]
    public function testMessagesAreTranslated(string $apiPath, string $locale, int $statusCode, string $message): void
    {
        $this->withHeadersForApiRequestWithAbility([])
            ->withHeader('Accept-Language', $locale)
            ->withExceptionHandling()
            ->get('api/' . $apiPath)
            ->assertStatus($statusCode)
            ->assertJson([
                'message' => $message,
            ]);
    }

    /**
     * @return array<int, array{string, string, int, string}>
     */
    public static function acceptLanguageHeaders(): array
    {
        return [
            ['404-path', 'de', Response::HTTP_NOT_FOUND, 'API-Endpunkt api/404-path existiert nicht.'],
            ['404-path', 'en', Response::HTTP_NOT_FOUND, 'API endpoint api/404-path does not exist.'],
            ['404-path', 'fr', Response::HTTP_NOT_FOUND, 'API-Endpunkt api/404-path existiert nicht.'], // German is default
            ['organizations', 'de_DE', Response::HTTP_FORBIDDEN, 'Token hat nicht die erforderlichen Berechtigung „Organisationen ansehen“.'],
            ['organizations', 'en_US', Response::HTTP_FORBIDDEN, 'Token does not have the required ability "View organizations".'],
        ];
    }

    public function testResponsesAreRateLimited(): void
    {
        $maxAttempts = 5;
        Config::set('api.throttle.max_attempts', $maxAttempts);
        Config::set('api.throttle.decay_minutes', 1);

        $withHeaders = $this->withHeadersForApiRequestWithAbility(Ability::ViewOrganizations);

        // Make the maximum number of attempts and assert decreasing remaining limit.
        $route = '/api/organizations';
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $withHeaders->get($route)
                ->assertOk()
                ->assertHeader('x-ratelimit-limit', $maxAttempts)
                ->assertHeader('x-ratelimit-remaining', $maxAttempts - $attempt);
        }

        // Make an additional request and assert status code 429.
        $withHeaders->get($route)
            ->assertTooManyRequests()
            ->assertHeader('x-ratelimit-limit', $maxAttempts)
            ->assertHeader('x-ratelimit-remaining', 0);
    }
}
