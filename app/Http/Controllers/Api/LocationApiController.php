<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SupportsIncludesInSnakeCase;
use App\Http\Requests\Filters\LocationFilterRequest;
use App\Http\Resources\LocationResource;
use App\Models\Location;
use Illuminate\Http\Resources\Json\JsonResource;

class LocationApiController extends Controller
{
    use SupportsIncludesInSnakeCase;

    /**
     * @return string[]
     */
    protected function allowedIncludeCounts(): array
    {
        return [
            'events',
            'organizations',
        ];
    }

    /**
     * @return string[]
     */
    protected function allowedIncludeRelations(): array
    {
        return [
            'organizations',
        ];
    }

    public function index(LocationFilterRequest $request): JsonResource
    {
        return LocationResource::collection(
            $this->loadPaginatedListWithIncludes($request, Location::buildQueryFromRequest())
        );
    }

    public function show(Location $location): JsonResource
    {
        $this->loadIncludesForModel($location);

        return new LocationResource($location);
    }
}
