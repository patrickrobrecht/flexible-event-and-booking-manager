<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class AcceptLanguageMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->parseAcceptLanguage($request);
        if (isset($locale)) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    private function parseAcceptLanguage(Request $request): ?string
    {
        $acceptedLanguages = explode(',', $request->server('HTTP_ACCEPT_LANGUAGE'));
        if (count($acceptedLanguages) === 0) {
            return null;
        }

        return Collection::make($acceptedLanguages)
            ->map(static function ($acceptedLanguage) {
                $localeParts = explode(';', $acceptedLanguage);
                if (isset($localeParts[1])) {
                    $factorParts = explode('=', $localeParts[1]);
                    $factor = (float) $factorParts[1];
                }

                return [
                    'locale' => trim($localeParts[0]),
                    'factor' => $factor ?? 1.0,
                ];
            })
            ->sortByDesc(static fn ($locale) => $locale['factor'])
            ->map(static fn ($locale) => substr($locale['locale'], 0, 2))
            ->first(static fn ($locale) => in_array($locale, self::allowedLocales(), true));
    }

    private static function allowedLocales(): array
    {
        return [
            config('app.locale'),
            config('app.fallback_locale'),
        ];
    }
}
