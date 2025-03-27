<?php

namespace App\Exceptions;

use App\Enums\Ability;
use App\Http\Middleware\AcceptLanguageMiddleware;
use App\Http\Middleware\JsonApiMiddleware;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Spatie\QueryBuilder\Exceptions\InvalidFilterQuery;
use Spatie\QueryBuilder\Exceptions\InvalidIncludeQuery;
use Spatie\QueryBuilder\Exceptions\InvalidQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnsupportedMediaTypeHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        InvalidQuery::class,
    ];

    /**
     * Render an exception into an HTTP response.
     *
     * @param Request $request
     *
     * @throws Throwable
     */
    public function render($request, Throwable $e): Response
    {
        if ($request->is('api/*')) {
            // Handle 401 (Unauthenticated errors)
            if ($e instanceof AuthenticationException) {
                return $this->renderApiError(Response::HTTP_UNAUTHORIZED, __('Unauthenticated - a valid Bearer token is required.'));
            }

            // Handle 403 (Forbidden) errors
            if ($e instanceof MissingAbilityException) {
                $ability = implode(', ', array_map(
                    static function (string $abilityValue) {
                        $ability = Ability::tryFrom($abilityValue);
                        return __('":name"', [
                            'name' => $ability === null ? $abilityValue : $ability->getTranslatedName(),
                        ]);
                    },
                    $e->abilities()
                ));
                return $this->renderApiError(
                    Response::HTTP_FORBIDDEN,
                    sprintf(__('Token does not have the required ability %s.'), $ability)
                );
            }

            // Handle 404 (Not Found) errors.
            if ($e instanceof NotFoundHttpException) {
                // Force API middleware even in error cases if no route matched!
                app(AcceptLanguageMiddleware::class)->handle($request, fn ($request) => parent::render($request, $e));
                app(JsonApiMiddleware::class)->handle($request, fn ($request) => parent::render($request, $e));
                return $this->renderApiErrorNotFound(
                    __('API endpoint :path does not exist.', [
                        'path' => $request->path(),
                    ])
                );
            }
            if ($e instanceof ModelNotFoundException) {
                return $this->renderApiErrorNotFound(
                    trans_choice(':model :ids do not exist.', count($e->getIds()), [
                        'model' => Str::of($e->getModel())->afterLast('\\')->snake(' ')->ucfirst(),
                        'ids' => implode(', ', $e->getIds()),
                    ])
                );
            }

            // Handle 415 (Unsupported media type) errors.
            if ($e instanceof UnsupportedMediaTypeHttpException) {
                return $this->renderApiError(Response::HTTP_UNSUPPORTED_MEDIA_TYPE, $e->getMessage());
            }

            // Handle 422 (Unprocessable Content) errors.
            if ($e instanceof InvalidFilterQuery) {
                return $this->renderApiErrorForInvalidQuery(['filter' => $e->getMessage()]);
            }
            if ($e instanceof InvalidIncludeQuery) {
                return $this->renderApiErrorForInvalidQuery(['include' => $e->getMessage()]);
            }
            if ($e instanceof ValidationException) {
                return $this->renderApiErrorForInvalidQuery($e->errors());
            }

            // Handle 429 (Too Many Requests) errors.
            if ($e instanceof ThrottleRequestsException) {
                $tokenId = (int) strtok($request->bearerToken(), '|');
                Log::info(sprintf('API: Too many requests for token %s', $tokenId));
                return $this->renderApiError(
                    Response::HTTP_TOO_MANY_REQUESTS,
                    sprintf(
                        __('Access limited to %d request(s) per %d minute(s).'),
                        config('api.throttle.max_attempts', 60),
                        config('api.throttle.decay_minutes', 1)
                    ),
                    headers: $e->getHeaders()
                );
            }

            // Handle other HTTP exceptions.
            if ($e instanceof HttpException) {
                return $this->renderApiError($e->getStatusCode(), $e->getMessage());
            }

            // Log any other error.
            Log::error(
                sprintf(
                    'Unhandled %s in API: %s',
                    $e::class,
                    $e->getMessage() === '' ? '[no message]' : $e->getMessage()
                )
            );
        }

        return parent::render($request, $e);
    }

    private function renderApiError(
        int $statusCode,
        string $message,
        array $errorDetails = [],
        array $headers = [],
    ): JsonResponse {
        $jsonData = [
            'message' => $message,
        ];

        if (count($errorDetails) > 0) {
            $jsonData['errors'] = $errorDetails;
        }

        return response()->json($jsonData, $statusCode, $headers, JSON_UNESCAPED_SLASHES);
    }

    private function renderApiErrorForInvalidQuery(array $errorDetails = []): JsonResponse
    {
        return $this->renderApiError(
            Response::HTTP_UNPROCESSABLE_ENTITY,
            __('Invalid query'),
            $errorDetails
        );
    }

    private function renderApiErrorNotFound(string $message): JsonResponse
    {
        return $this->renderApiError(Response::HTTP_NOT_FOUND, $message);
    }
}
