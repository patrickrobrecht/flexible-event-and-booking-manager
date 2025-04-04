<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;

class JsonApiMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->acceptsJson() && !$request->wantsJson()) {
            throw new UnsupportedMediaTypeHttpException('Unsupported Media Type. Accepted formats: [application/json]');
        }

        return $next($request);
    }
}
