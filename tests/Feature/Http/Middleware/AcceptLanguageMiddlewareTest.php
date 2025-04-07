<?php

namespace Tests\Feature\Http\Middleware;

use App;
use App\Http\Middleware\AcceptLanguageMiddleware;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

#[CoversClass(AcceptLanguageMiddleware::class)]
class AcceptLanguageMiddlewareTest extends TestCase
{
    #[DataProvider('acceptLanguageHeaders')]
    public function testPreferredLocaleSetDependingOnAcceptLanguageHeader(?string $acceptLanguageHeader, string $preferredLocale): void
    {
        $request = Request::create('/test', server: ['HTTP_ACCEPT_LANGUAGE' => $acceptLanguageHeader]);
        (new AcceptLanguageMiddleware())->handle($request, fn () => response()->json(['message' => 'Success']));

        $this->assertEquals($preferredLocale, App::getLocale());
    }

    /**
     * @return array<int, array<int, string>>
     */
    public static function acceptLanguageHeaders(): array
    {
        return [
            // Single locale
            ['de', 'de'],
            ['en', 'en'],
            // Locale with country
            ['de-DE', 'de'],
            ['de_DE', 'de'],
            // Preferred language with alternatives
            ['de-DE, de;q=0.9, en-US;q=0.8, en;q=0.7', 'de'],
            ['en-GB, de;q=0.9, en-US;q=0.8, en;q=0.7', 'en'],
            // Multiple languages with factor
            ['de-DE;q=0.8, de;q=0.7, en-US;q=0.9, en;q=0.7', 'en'],
        ];
    }
}
