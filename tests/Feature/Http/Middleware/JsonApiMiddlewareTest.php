<?php

/** @noinspection SensitiveParameterInspection */

namespace Tests\Feature\Http\Middleware;

use App\Http\Middleware\JsonApiMiddleware;
use Illuminate\Http\Request;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Tests\TestCase;

#[CoversClass(JsonApiMiddleware::class)]
class JsonApiMiddlewareTest extends TestCase
{
    public function testNoAcceptHeader(): void
    {
        $request = Request::create('/test');
        $response = (new JsonApiMiddleware())->handle($request, fn () => response()->json(['message' => 'Success']));

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    #[DataProvider('validAcceptHeaders')]
    public function testValidAcceptHeader(string $acceptHeader): void
    {
        $request = Request::create('/test', server: ['HTTP_ACCEPT' => $acceptHeader]);
        $response = (new JsonApiMiddleware())->handle($request, fn () => response()->json(['message' => 'Success']));

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
    }

    public static function validAcceptHeaders(): array
    {
        return [
            // any type accepted
            ['*'],
            ['*/*'],
            ['application/*'],
            // JSON is the requested type
            ['application/json'],
            ['application/vnd.api+json'],
            // JSON is one of many possible types
            ['text/html, application/json;q=0.9'],
            // any is one of many possible types
            ['text/xml, application/xml;q=0.9, */*;q=0.8'],
        ];
    }

    #[DataProvider('invalidAcceptHeaders')]
    public function testInvalidAcceptHeader(string $acceptHeader): void
    {
        $this->expectException(UnsupportedMediaTypeHttpException::class);

        $request = Request::create('/test', server: ['HTTP_ACCEPT' => $acceptHeader]);
        $middleware = new JsonApiMiddleware();

        $middleware->handle($request, static function ($req) {
            return response()->json(['message' => 'Error']);
        });
    }

    public static function invalidAcceptHeaders(): array
    {
        return [
            ['application/xml'],
            ['text/plain'],
            ['text/html, application/xhtml+xml, application/xml;q=0.9'],
        ];
    }
}
