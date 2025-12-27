<?php

namespace App\Http\Controllers\Api;

use App\Enums\Ability;
use App\Enums\Visibility;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SupportsIncludesInSnakeCase;
use App\Http\Requests\Filters\EventFilterRequest;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Laravel\Sanctum\Exceptions\MissingAbilityException;

class EventApiController extends Controller
{
    use SupportsIncludesInSnakeCase;

    /**
     * @return string[]
     */
    protected function allowedIncludeCounts(): array
    {
        return [
            'subEvents',
        ];
    }

    /**
     * @return string[]
     */
    protected function allowedIncludeRelations(): array
    {
        return [
            'eventSeries',
            'location',
            'organization.location',
            'parentEvent',
            'subEvents',
        ];
    }

    public function index(EventFilterRequest $request): JsonResource
    {
        return EventResource::collection(
            $this->loadPaginatedListWithIncludes($request, Event::buildQueryFromRequest())
        );
    }

    public function show(Event $event, FormRequest $request): JsonResource
    {
        if (
            $event->visibility === Visibility::Private
            && ($request->user()?->tokenCant(Ability::ViewPrivateEvents->value) ?? false)
        ) {
            throw new MissingAbilityException(Ability::ViewPrivateEvents->value);
        }

        $this->loadIncludesForModel($event);

        return new EventResource($event);
    }
}
