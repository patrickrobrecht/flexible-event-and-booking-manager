<?php

namespace App\Http\Controllers;

use App\Listeners\CacheOpenApiDocListener;
use App\Models\PersonalAccessToken;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class ApiDocumentationController extends Controller
{
    public function index(): View
    {
        $this->authorize('create', PersonalAccessToken::class);

        return view('docs.docs');
    }

    public function spec(): Response
    {
        $this->authorize('create', PersonalAccessToken::class);

        return response()->make(self::getYamlFileContents(), SymfonyResponse::HTTP_OK, [
            'Content-Type: text/yaml',
        ]);
    }

    private static function getYamlFileContents(): string
    {
        if (config('app.debug')) {
            return CacheOpenApiDocListener::cacheConfigurationFile();
        }

        return Cache::rememberForever(
            'open-api-spec',
            static fn () => CacheOpenApiDocListener::cacheConfigurationFile()
        );
    }
}
