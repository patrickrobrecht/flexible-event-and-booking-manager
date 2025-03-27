<?php

use App\Exceptions\Handler;
use App\Http\Middleware\AcceptLanguageMiddleware;
use App\Http\Middleware\JsonApiMiddleware;
use App\Http\Middleware\ThrottleRequestsMiddleware;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Session\Middleware\AuthenticateSession;
use Laravel\Sanctum\Http\Middleware\CheckAbilities;
use Laravel\Sanctum\Http\Middleware\CheckForAnyAbility;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware
            ->web(
                append: [
                    AuthenticateSession::class,
                ]
            )
            ->api(
                prepend: [
                    AcceptLanguageMiddleware::class,
                    JsonApiMiddleware::class,
                    'auth:sanctum',
                    EnsureFrontendRequestsAreStateful::class,
                    ThrottleRequestsMiddleware::class,
                ]
            )
            ->alias([
                'abilities' => CheckAbilities::class,
                'ability' => CheckForAnyAbility::class,
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (Illuminate\Http\Exceptions\PostTooLargeException $e, $request) {
            return back();
        });
    })
    ->withSingletons([
        ExceptionHandler::class => Handler::class,
    ])
    ->create();
