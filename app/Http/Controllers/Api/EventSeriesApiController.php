<?php

namespace App\Http\Controllers\Api;

use App\Enums\Ability;
use App\Enums\Visibility;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SupportsIncludesInSnakeCase;
use App\Http\Requests\Filters\EventSeriesFilterRequest;
use App\Http\Resources\EventSeriesResource;
use App\Models\EventSeries;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\Exceptions\MissingAbilityException;

class EventSeriesApiController extends Controller
{
    use SupportsIncludesInSnakeCase;

    protected function allowedIncludeCounts(): array
    {
        return [
            'events',
            'subEventSeries',
        ];
    }

    protected function allowedIncludeRelations(): array
    {
        return [
            'organization.location',
            'parentEventSeries',
            'subEventSeries',
        ];
    }

    public function index(EventSeriesFilterRequest $request): JsonResource
    {
        return EventSeriesResource::collection(
            $this->loadPaginatedListWithIncludes($request, EventSeries::buildQueryFromRequest())
        );
    }

    public function show(EventSeries $eventSeries, FormRequest $request): JsonResource
    {
        if ($eventSeries->visibility === Visibility::Private && $request->user()->tokenCant(Ability::ViewPrivateEventSeries->value)) {
            throw new MissingAbilityException(Ability::ViewPrivateEventSeries->value);
        }

        $this->loadIncludesForModel($eventSeries);

        return new EventSeriesResource($eventSeries);
    }
}
