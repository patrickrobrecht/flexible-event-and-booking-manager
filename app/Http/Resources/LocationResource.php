<?php

namespace App\Http\Resources;

use App\Http\Resources\Traits\BuildsResource;
use App\Models\Location;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Location
 */
class LocationResource extends JsonResource
{
    use BuildsResource;

    /**
     * @return array<string, mixed>
     */
    public function relationsToArray(): array
    {
        return [
            'organizations' => OrganizationResource::collection($this->whenLoaded('organizations')),
        ];
    }
}
