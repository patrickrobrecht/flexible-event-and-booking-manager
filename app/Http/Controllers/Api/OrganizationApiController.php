<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\SupportsIncludesInSnakeCase;
use App\Http\Requests\Filters\OrganizationFilterRequest;
use App\Http\Resources\OrganizationResource;
use App\Models\Organization;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizationApiController extends Controller
{
    use SupportsIncludesInSnakeCase;

    /**
     * @return string[]
     */
    protected function allowedIncludeCounts(): array
    {
        return [
            'events',
            'eventSeries',
        ];
    }

    /**
     * @return string[]
     */
    protected function allowedIncludeRelations(): array
    {
        return [
            'location',
        ];
    }

    public function index(OrganizationFilterRequest $request): JsonResource
    {
        return OrganizationResource::collection(
            $this->loadPaginatedListWithIncludes($request, Organization::buildQueryFromRequest())
        );
    }

    public function show(Organization $organization): JsonResource
    {
        $this->loadIncludesForModel($organization);

        return new OrganizationResource($organization);
    }
}
